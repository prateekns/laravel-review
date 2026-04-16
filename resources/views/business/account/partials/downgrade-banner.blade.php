@if($isSubscribed)
<div class="bg-[#FFFAE8] border border-[#DFB400] text-[14px] font-[500]  text-[#212529] rounded-lg p-[24px] w-full mt-[28px]" id="plan-info-banner">
    <div class="flex items-start flex-wrap gap-x-6 gap-y-2 flex-col">
        <!-- Left Side: Icon and Info -->
        <div class="flex items-center gap-3">
            <!-- Custom SVG icon to match the design -->
            <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="25" height="24" rx="12" fill="#DFB400" />
                <path d="M12.5 11.1871V13.2362M7.41636 15.0808C6.94348 15.9005 7.53504 16.9247 8.48132 16.9247H16.5187C17.465 16.9247 18.0565 15.9005 17.5836 15.0808L13.565 8.11508C13.0918 7.29497 11.9082 7.29497 11.435 8.11508L7.41636 15.0808ZM12.5 14.8755H12.5041V14.8796H12.5V14.8755Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>

            <!-- Dynamic user subscription info -->
            <p>
                You have current subscription plan for {{ $user->business->num_admin }} admin, {{ $user->business->num_technician }} technician
            </p>
        </div>

        <p>
            {{ __('To downgrade, reduce the number of Admins or Technicians you\'d like to pay for. You’ll only be billed for what you use going forward.') }}
        </p>

        <!-- Right Side: Next Payment Info -->
        <p>
            <span class="text-[#B32318]">
                Note:
            </span>
            {{ __('All the Admin and Technician users will be marked Inactive. Make sure to activate the relevant profiles based on the downgraded plan.') }}
        </p>
    </div>
</div>
@endif
