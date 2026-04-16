document.addEventListener("alpine:init", () => {
    // Create a global reference to the scheduler component
    let schedulerComponent = null;

    // Listen for notification events from Livewire
    Livewire.on("notify", (event) => {
        // Clear drag visual states on any notification (success/error/info) to ensure UI resets
        const notification = event[0];
        if (notification && schedulerComponent) {
            schedulerComponent.clearDragVisualStates();
            schedulerComponent.clearDragOrigin();
        }
    });

    Alpine.data("scheduler", () => ({
        // State variables
        dragoverTechId: null,
        dragoverDateIso: null,
        isDraggingOverUnassigned: false,
        isSubmittingAssignment: false,
        isPendingAssignment: false,
        expandedTechs: {},
        dragOrigin: null, // Track original position of dragged job
        isPanning: false,
        panningContainer: null,
        panningStartX: 0,
        panningScrollLeft: 0,
        draggingJobId: null,
        dragSource: null,
        // Initialize component
        init() {
            // Store reference to this component instance
            schedulerComponent = this;
            // Initialize expandedTechs for all technicians
            this.expandedTechs = {};
        },

        // Drag and drop handlers
        handleDragStart(
            {
                event,
                jobId,
                source,
                instanceId = null,
                originalTechId = null,
                originalDateIso = null,
                time = null,
                assignAllFuture = false
            }
        ) {
            // Check if the element is actually draggable
            if (!event.target.getAttribute("draggable")) {
                event.preventDefault();
                return;
            }

            // Don't allow dragging if jobs are not expanded
            const jobContainer = event.target.closest(".job-container");
            const techId =
                jobContainer?.closest("[data-tech-id]")?.dataset?.techId;
            if (techId && !this.expandedTechs[techId]) {
                event.preventDefault();
                return;
            }

            // Store original position
            this.dragOrigin = {
                jobId: jobId,
                source: source,
                instanceId: instanceId,
                techId: originalTechId,
                dateIso: originalDateIso,
                time: time,
                assignAllFuture: assignAllFuture,
                index: Array.from(jobContainer?.children || []).findIndex(
                    (child) => child.querySelector(`[data-job-id="${jobId}"]`)
                ),
            };

            // Store in event data
            event.dataTransfer.effectAllowed = "move";
            event.dataTransfer.setData("text/plain", jobId.toString());
            if (instanceId) {
                event.dataTransfer.setData(
                    "application/instance-id",
                    instanceId.toString()
                );
            }

            if (time) {
                event.dataTransfer.setData("application/time", time.toString());
            }

            if (assignAllFuture) {
                event.dataTransfer.setData(
                    "application/assignAllFuture",
                    assignAllFuture
                );
            }

            // Add visual feedback
            event.target.classList.add("dragging");
            event.target.classList.add("opacity-50");
        },

        handleDragEnd(event) {
            // Clear drag origin and visual states when drag ends (including cancellation)
            this.dragOrigin = null;
            this.clearDragVisualStates();
        },

        clearDragVisualStates() {
            // Reset Alpine.js state
            this.dragoverTechId = null;
            this.dragoverDateIso = null;
            this.isDraggingOverUnassigned = false;
            this.isSubmittingAssignment = false;
            this.isPendingAssignment = false;
            // Don't clear dragOrigin here - it's needed for drop processing
            // this.dragOrigin = null; // Clear drag origin

            // Clear the dragging class and opacity from job elements
            const draggingElements = document.querySelectorAll(".dragging");
            draggingElements.forEach((el) => {
                el.classList.remove("dragging", "opacity-50");
            });

            // Also clear any drag-over visual effects
            const dragOverElements = document.querySelectorAll(
                ".drag-over-slot",
                ".drag-over-unassigned"
            );
            dragOverElements.forEach((el) => {
                el.classList.remove("drag-over-slot", "drag-over-unassigned");
            });
        },

        clearDragOrigin() {
            this.dragOrigin = null;
        },

        setPendingAssignment() {
            this.isPendingAssignment = true;
            // Confirm popup is about to be shown, stop the submission overlay
            this.isSubmittingAssignment = false;
            // Don't clear visual states here - keep them until user cancels
        },

        resetPendingAssignment() {
            this.isPendingAssignment = false;
            // Clear visual states when assignment is confirmed
            this.clearDragVisualStates();
        },

        handleDragOverSlot(techId, dateIso, isNotAvailable) {
            // Check if there's actually a draggable element being dragged
            const draggedElement = document.querySelector(".dragging");
            if (!draggedElement?.getAttribute("draggable")) {
                return;
            }

            if (isNotAvailable || this.isPendingAssignment) return;

            // If it's an assigned job, only allow dragging within same tech/date
            if (this.dragOrigin && this.dragOrigin.source === "assigned") {
                if (
                    this.dragOrigin.techId === techId &&
                    this.dragOrigin.dateIso === dateIso
                ) {
                    this.dragoverTechId = techId;
                    this.dragoverDateIso = dateIso;
                }
                return;
            }

            // For unassigned jobs, use existing assignment logic
            this.dragoverTechId = techId;
            this.dragoverDateIso = dateIso;
        },

        handleDragLeaveSlot() {
            this.dragoverTechId = null;
            this.dragoverDateIso = null;
        },

        handleDropInSlot(event, techId, dateIso, isNotAvailable) {
            // First check if the dragged element is actually draggable
            const draggedElement = document.querySelector(".dragging");
            if (!draggedElement?.getAttribute("draggable")) {
                return;
            }

            if (isNotAvailable) {
                // Show notification for not available slot
                this.$wire.showNotAvailableNotification();
                return;
            }

            const jobId = event.dataTransfer.getData("text/plain");
            const instanceId =
                event.dataTransfer.getData("application/instance-id") || null;
            const time = event.dataTransfer.getData("application/time") || null;
            const assignAllFuture =
                event.dataTransfer.getData("application/assignAllFuture") ||
                false;

            // Handle sorting within the same tech/date
            if (this.dragOrigin && this.dragOrigin.source === "assigned") {
                this.handleJobsSorting(event, jobId, techId, dateIso);
                return;
            }

            // Check if dropping back to original position
            if (
                this.dragOrigin &&
                this.dragOrigin.jobId == jobId &&
                this.dragOrigin.techId == techId &&
                this.dragOrigin.dateIso === dateIso
            ) {
                this.clearDragVisualStates();
                this.dragOrigin = null;
                return;
            }

            if (jobId) {
                // Call Livewire method directly for new assignments
                this.isSubmittingAssignment = true;
                this.$wire
                    .assignJob(
                        jobId,
                        techId,
                        dateIso,
                        instanceId,
                        time,
                        assignAllFuture
                    )
                    .catch(() => {
                        // Clear visual states if there's an error
                        this.clearDragVisualStates();
                        this.clearDragOrigin();
                    })
                    .finally(() => {
                        // Stop the loader when server returns
                        this.isSubmittingAssignment = false;
                    });
            }

            // Clear drag origin after processing
            this.dragOrigin = null;
        },

        handleJobsSorting(event, jobId, techId, dateIso) {
            // Only allow sorting within the same tech/date
            if (
                this.dragOrigin.techId === techId &&
                this.dragOrigin.dateIso === dateIso
            ) {
                const jobContainer = event.target.closest(".job-container");

                if (jobContainer) {
                    // Get all visible job elements
                    const jobElements = Array.from(
                        jobContainer.children
                    ).filter((child) => {
                        // Check if the element is visible
                        const style = window.getComputedStyle(child);
                        if (style.display === "none") return false;

                        // Check if it has a visible job-touch element
                        const jobTouch = child.querySelector(".job-touch");
                        if (!jobTouch) return false;

                        const jobTouchStyle = window.getComputedStyle(jobTouch);
                        return jobTouchStyle.display !== "none";
                    });

                    // Get the drop target
                    let newPosition = this.getDropTarget(event, jobElements);

                    // Find the current position of the dragged item
                    const oldPosition = jobElements.findIndex((el) =>
                        el.querySelector(`[data-job-id="${jobId}"]`)
                    );

                    if (oldPosition !== -1 && newPosition !== oldPosition) {
                        // Call Livewire method to update order
                        this.$wire
                            .updateJobOrder(
                                jobId,
                                techId,
                                dateIso,
                                newPosition,
                                oldPosition
                            )
                            .then(() => {
                                // Force a refresh of the job container
                                this.$wire.$refresh();
                            })
                            .catch((error) => {
                                console.error("Failed to update order:", error);
                            });
                    }
                }
            }
            this.clearDragVisualStates();
            this.clearDragOrigin();
        },

        getDropTarget(event, jobElements) {
            // Get the drop target
            const dropTarget = event.target.closest(".job-touch");
            let newPosition = jobElements.length; // Default to last position

            if (dropTarget) {
                const rect = dropTarget.getBoundingClientRect();
                const mouseY = event.clientY;
                const midPoint = rect.top + rect.height / 2;

                // Find the wrapper of the drop target
                const dropTargetWrapper = dropTarget.closest("[x-show]");
                const targetIndex = jobElements.findIndex(
                    (el) => el === dropTargetWrapper
                );

                if (targetIndex !== -1) {
                    // If dropping on a job, position before or after based on mouse position
                    newPosition =
                        mouseY > midPoint ? targetIndex + 1 : targetIndex;
                } else {
                    // If dropping on container, find nearest job
                    let closestDistance = Infinity;
                    let closestIndex = 0;

                    jobElements.forEach((el, index) => {
                        const elRect = el.getBoundingClientRect();
                        const distance = Math.abs(
                            mouseY - (elRect.top + elRect.height / 2)
                        );
                        if (distance < closestDistance) {
                            closestDistance = distance;
                            closestIndex =
                                mouseY > elRect.top + elRect.height / 2
                                    ? index + 1
                                    : index;
                        }
                    });

                    newPosition = closestIndex;
                }

                // Clamp position to valid range
                newPosition = Math.max(
                    0,
                    Math.min(newPosition, jobElements.length)
                );
            }

            return newPosition;
        },

        // Scroll handling during drag
        handleDragScroll(event) {
            if (!event) return;

            const scrollZone = 60;
            const scrollSpeed = 15;
            const windowHeight = window.innerHeight;
            const clientY = event.clientY;

            if (clientY < scrollZone || clientY > windowHeight - scrollZone) {
                // Simple scroll handling - you can enhance this if needed
                const scrollDirection = clientY < scrollZone ? -1 : 1;
                window.scrollBy(0, scrollDirection * scrollSpeed);
            }
        },
        handleTouchStart(event, jobId, source) {
            this.draggingJobId = jobId;
            this.dragSource = source;
            const el = event.target.closest('[draggable="true"]');
            if (el) el.classList.add("dragging");
        },

        // Get element at touch point
        getElementAtTouch(touch, draggingEl) {
            if (draggingEl) draggingEl.style.pointerEvents = "none";
            const targetEl = document.elementFromPoint(
                touch.clientX,
                touch.clientY
            );
            if (draggingEl) draggingEl.style.pointerEvents = "auto";
            return targetEl;
        },

        // Handle dragging over daily slot
        handleDailySlotDrag(slot) {
            if (slot && !slot.classList.contains("day-not-available")) {
                this.dragoverTechId = parseInt(slot.dataset.techId);
                this.dragoverDateIso = slot.dataset.dateIso;
                this.isDraggingOverUnassigned = false;
                return true;
            }
            return false;
        },

        // Reset drag state
        resetDragState() {
            this.dragoverTechId = null;
            this.dragoverDateIso = null;
            this.isDraggingOverUnassigned = false;
        },

        // Handle touch move during job drag
        handleJobDrag(event) {
            this.handleDragScroll(event);
            const touch = event.touches[0];
            const draggingEl = this.$el.querySelector(".dragging");

            const targetEl = this.getElementAtTouch(touch, draggingEl);
            if (!targetEl) return;

            const slot = targetEl.closest(".daily-slot");
            if (!this.handleDailySlotDrag(slot)) {
                this.resetDragState();
            }
        },

        // Main touch move handler
        handleTouchMove(event) {
            if (this.draggingJobId) {
                this.handleJobDrag(event);
            } else if (this.isPanning) {
                this.panMove(event);
            }
        },
        // Get dragging element and its details
        getDraggingDetails() {
            const draggingEl =
                this.$el.querySelector(".dragging") ||
                document.querySelector(".dragging");
            return {
                element: draggingEl,
                jobId: this.draggingJobId,
                instanceId: draggingEl?.dataset?.instanceId || null,
            };
        },

        // Handle job assignment to technician
        handleJobAssignment(jobDetails) {
            if (this.dragSource === "assigned") {
                this.clearDragVisualStates();
                this.clearDragOrigin();
                return;
            }

            window.dispatchEvent(
                new CustomEvent("scheduler-assign-job", {
                    detail: {
                        jobId: jobDetails.jobId,
                        techId: this.dragoverTechId,
                        dateIso: this.dragoverDateIso,
                        instanceId: jobDetails.instanceId,
                        time:
                            document.querySelector(
                                `[draggable="true"][data-job-id="${jobDetails.jobId}"]`
                            )?.dataset?.time || null,
                        assignAllFuture: jobDetails.assignAllFuture,
                    },
                })
            );
        },

        // Clear drag state
        clearDragState(draggingEl) {
            this.draggingJobId = null;
            this.dragSource = null;
            if (draggingEl) {
                draggingEl.classList.remove("dragging");
            }
        },

        // Main touch end handler
        handleTouchEnd(event) {
            if (!this.draggingJobId) {
                this.panEnd();
                return;
            }

            const {
                element: draggingEl,
                jobId,
                instanceId,
            } = this.getDraggingDetails();

            if (this.dragoverTechId && this.dragoverDateIso) {
                this.handleJobAssignment({ jobId, instanceId });
            } else {
                this.handleDragEnd(event);
            }

            this.clearDragState(draggingEl);
            this.panEnd();
        },

        // Panning handling
        panStart(event, containerRefName) {
            if (event.target.closest('[draggable="true"]')) {
                return;
            }
            // Do not start panning when tapping interactive controls
            if (
                event.target.closest(
                    'button, a, input, select, textarea, [role="button"], .getjobsforday'
                )
            ) {
                return;
            }
            // preventDefault is called from a non-passive listener at call site
            this.isPanning = true;
            this.panningContainer = this.$refs[containerRefName];
            this.panningStartX =
                (event.touches ? event.touches[0].pageX : event.pageX) -
                this.panningContainer.offsetLeft;
            this.panningScrollLeft = this.panningContainer.scrollLeft;
        },

        panMove(event) {
            if (!this.isPanning) return;
            const x =
                (event.touches ? event.touches[0].pageX : event.pageX) -
                this.panningContainer.offsetLeft;
            const walk = x - this.panningStartX;
            this.panningContainer.scrollLeft = this.panningScrollLeft - walk;
        },

        panEnd() {
            if (!this.isPanning) return;
            this.isPanning = false;
            if (this.panningContainer) {
                this.panningContainer = null;
            }
        },
    }));
});

// Initialize confirmation dialogs (if needed in the future)
window.addEventListener("show-confirmation", (event) => {
    // For now, we'll use browser confirm
    if (confirm(event.detail.message)) {
        Livewire.dispatch(event.detail.onConfirm, event.detail.data);
    }
});

// Handle drag scroll
window.addEventListener("drag-scroll", (event) => {
    const { clientY, windowHeight, scrollSpeed } = event.detail;
    const scrollZone = 60;

    if (clientY < scrollZone) {
        window.scrollBy(0, -scrollSpeed);
    } else if (clientY > windowHeight - scrollZone) {
        window.scrollBy(0, scrollSpeed);
    }
});
