<script setup lang="ts">
import { useCart } from '@/composables/useCart';
import { Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const { itemCount } = useCart();

const isNavOpen = ref(false);
const isBarActive = ref(false);
const isLgScreen = ref(false);

const navLinks = [
    { label: 'Home', routeName: 'home' },
    { label: 'Shop', routeName: 'shop' },
    { label: 'Blog', routeName: 'blog' },
    { label: 'About', routeName: 'about' },
    { label: 'Contact Us', routeName: 'contact' },
] as const;

function isActive(routeName: string): boolean {
    try {
        if (routeName === 'shop') {
            return route().current('shop') || route().current('shop.product') || false;
        }

        return route().current(routeName) ?? false;
    } catch {
        return false;
    }
}

function toggleNav(): void {
    isNavOpen.value = !isNavOpen.value;
    isBarActive.value = isNavOpen.value;
}

function closeNav(): void {
    isNavOpen.value = false;
    isBarActive.value = false;
}

function updateBreakpoint(): void {
    isLgScreen.value = window.matchMedia('(min-width: 992px)').matches;

    if (isLgScreen.value) {
        closeNav();
    }
}

onMounted(() => {
    updateBreakpoint();
    window.addEventListener('resize', updateBreakpoint);
});

onUnmounted(() => {
    window.removeEventListener('resize', updateBreakpoint);
});
</script>

<template>
    <nav class="shop-header navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
        <div class="container">
            <Link class="navbar-brand" :href="route('home')" @click="closeNav">
                <img src="/img/logo1.png" alt="Rymo logo" />
            </Link>

            <button
                class="navbar-toggler"
                type="button"
                aria-controls="navbarSupportedContent"
                :aria-expanded="isNavOpen"
                aria-label="Toggle navigation"
                @click="toggleNav"
            >
                <span><i id="bar" class="fa fa-bars" :class="{ active: isBarActive }" aria-hidden="true"></i></span>
            </button>
            <div
                id="navbarSupportedContent"
                class="navbar-collapse shop-nav-collapse"
                :class="{ 'shop-nav-collapse--open': isLgScreen || isNavOpen }"
            >
                <ul class="navbar-nav ml-auto">
                    <li v-for="link in navLinks" :key="link.label" class="nav-item">
                        <Link
                            v-if="link.routeName"
                            :href="route(link.routeName)"
                            class="nav-link"
                            :class="{ active: isActive(link.routeName) }"
                            @click="closeNav"
                        >
                            {{ link.label }}
                        </Link>
                        <a v-else class="nav-link" href="#" @click="closeNav">{{ link.label }}</a>
                    </li>
                    <li class="nav-item">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <Link :href="route('cart')" class="cart-link" @click="closeNav">
                            <i class="fa fa-shopping-bag" aria-hidden="true" :class="{ active: isActive('cart') }"></i>
                            <span v-if="itemCount > 0" class="cart-badge">{{ itemCount }}</span>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<style scoped>
.navbar {
    font-size: 16px;
    top: 0;
    left: 0;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}

.navbar-light .navbar-nav .nav-link {
    padding: 0 20px;
    color: black;
    transition: 0.3s ease;
}

.navbar-light .navbar-nav .nav-link:hover,
.navbar-light .navbar-nav .nav-link.active,
.navbar i:hover,
.navbar i.active {
    color: coral;
}

.navbar i {
    font-size: 1.2rem;
    margin: 0 20px;
    cursor: pointer;
    font-weight: 500;
    transition: 0.3s ease;
    color: black;
}

.cart-link {
    position: relative;
    display: inline-flex;
    align-items: center;
    text-decoration: none;
}

.cart-badge {
    position: absolute;
    top: -6px;
    right: 8px;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 999px;
    background-color: #fb774b;
    color: #fff;
    font-size: 0.65rem;
    font-weight: 700;
    line-height: 18px;
    text-align: center;
    pointer-events: none;
}

.navbar-light .navbar-toggler {
    border: none;
    outline: none;
}

#bar {
    font-size: 1.5rem;
    padding: 7px;
    cursor: pointer;
    font-weight: 500;
    transition: 0.3s ease;
    color: black;
}

.navbar-toggler:hover #bar,
#bar.active {
    color: white;
}

@media (min-width: 992px) {
    .shop-header.navbar-expand-lg .shop-nav-collapse {
        display: flex !important;
        flex-basis: auto;
        flex-grow: 1;
        align-items: center;
    }

    .shop-header .navbar-toggler {
        display: none;
    }
}

@media (max-width: 991.98px) {
    .shop-header.navbar-expand-lg .shop-nav-collapse {
        display: none;
    }

    .shop-header.navbar-expand-lg .shop-nav-collapse.shop-nav-collapse--open {
        display: block;
    }
}

@media only screen and (max-width: 991px) {
    .shop-header .navbar-toggler:hover,
    .shop-header .navbar-toggler:focus {
        background-color: #fb774b;
    }

    .shop-header .navbar-toggler:hover #bar,
    .shop-header .navbar-toggler:focus #bar {
        color: #fff;
    }

    #navbarSupportedContent > ul {
        margin: 1rem;
        justify-content: flex-end;
        align-items: flex-end;
        text-align: right;
    }

    #navbarSupportedContent > ul > li:nth-child(n) > a {
        padding: 10px 0;
    }
}
</style>
