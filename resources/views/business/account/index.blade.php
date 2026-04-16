@extends('layouts.business.app')

@section('title', 'Business Dashboard')
@section('scripts')
<script>
  function setupAsideMenuToggle() {
    const hamburgerIcon = document.querySelector(".hamburger-icon");
    const mobileMenu = document.querySelector(".mobile-menu");
    const mobileMenuOverlay = document.querySelector(".mobile-menu-overlay");

    if (!hamburgerIcon || !mobileMenu || !mobileMenuOverlay) return;

    function toggleMenu() {
      if (window.innerWidth <= 1023) {
        mobileMenu.classList.toggle("translate-x-0");
        mobileMenuOverlay.classList.toggle("hidden");
      }
    }

    function hideMenu() {
      if (window.innerWidth <= 1023) {
        mobileMenu.classList.remove("translate-x-0");
        mobileMenuOverlay.classList.add("hidden");
      }
    }

    hamburgerIcon.addEventListener("click", toggleMenu);
    mobileMenuOverlay.addEventListener("click", hideMenu);
  }

  // Run after the DOM is ready
  document.addEventListener("DOMContentLoaded", function() {
    setupAsideMenuToggle();
  });
</script>
@endsection
@section('content')

    <div class="w-full">
        <div class="py-2 sm:flex">
            <div class="flex justify-between  flex-col  mb-[24px]">
                <h1 class="main-heading">{{ __('Account Settings') }}</h1>
                <p class="sub-heading">{{ __('Manage your profile, subscription, and preferences — all in one place.') }}</p>
            </div>
        </div>
        <livewire:business.account.account />
    </div>
@endsection
