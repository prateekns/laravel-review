export function multiSelect() {
    return {
        options: [],
        selected: [],
        show: false,
        old_value: this.$refs.old_value?.value || '',
        open() { this.show = true },
        close() { this.show = false },
        isOpen() { return this.show === true },
        select(index, event) {
            if (!this.options[index].selected) {
                this.options[index].selected = true;
                this.options[index].element = event.target;
                this.selected.push(index);
            } else {
                this.selected.splice(this.selected.lastIndexOf(index), 1);
                this.options[index].selected = false
            }
            this.updateValue();
        },
        remove(index, option) {
            this.options[option].selected = false;
            this.selected.splice(index, 1);
            this.updateValue();
        },
        loadOptions() {
            const options = document.getElementById('select').options;
            for (let i = 0; i < options.length; i++) {
                const isSelected = options[i].hasAttribute('selected');
                this.options.push({
                    value: options[i].value,
                    text: options[i].innerText,
                    selected: isSelected
                });
                if (isSelected) {
                    this.selected.push(i);
                }
            }
            // Handle old_value if it exists and no pre-selected options were found
            if (this.old_value && this.selected.length === 0) {
                this.selected = this.old_value.includes(",") ? 
                    this.old_value.split(",").map(item => parseInt(item) - 1) : 
                    [parseInt(this.old_value) - 1];
                // Mark the options as selected
                this.selected.forEach(index => {
                    if (this.options[index]) {
                        this.options[index].selected = true;
                    }
                });
            }
        },
        updateValue() {
            const selectedValues = this.selected.map((option) => this.options[option].value);
            this.$dispatch('multi-selected-value', { value: selectedValues });
        },
        selectedValues() {
            return this.selected.map((option) => this.options[option].value);
        }
    }
}
