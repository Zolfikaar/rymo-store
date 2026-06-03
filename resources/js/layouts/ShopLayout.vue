<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

withDefaults(
    defineProps<{
        flushMain?: boolean;
    }>(),
    {
        flushMain: false,
    },
);

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

const footerFeaturedLinks = ['Men', 'Women', 'Boys', 'Girls', 'New Arraivals', 'Shoes', 'Clothes'];

const instagramImages = [
    '/img/insta/1.jpg',
    '/img/insta/2.jpg',
    '/img/insta/3.jpg',
    '/img/insta/4.jpg',
    '/img/insta/5.jpg',
];
</script>

<template>
    <div class="shop-layout">
        <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
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
                            <Link :href="route('cart')" @click="closeNav">
                                <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="shop-main" :class="{ 'shop-main--flush': flushMain }">
            <slot />
        </main>

        <footer class="mt-5 py-5">
            <div class="row container mx-auto pt-5">
                <div class="footer-one col-lg-3 col-md-6 col-12">
                    <img src="/img/logo2.png" alt="Rymo" />
                    <p class="pt-3">
                        Lorem, ipsum dolor sit amet consectetur adipisicing elit. Commodi nostrum rem velit asperiores.
                        Quos, asperiores?
                    </p>
                </div>
                <div class="footer-one col-lg-3 col-md-6 col-12 mb-3">
                    <h5 class="pb-2">Featured</h5>
                    <ul class="featured-list text-uppercase list-unstyled">
                        <li v-for="item in footerFeaturedLinks" :key="item">
                            <a href="#">{{ item }}</a>
                        </li>
                    </ul>
                </div>
                <div class="footer-one col-lg-3 col-md-6 col-12">
                    <h5 class="pb-2">Contact Us</h5>
                    <div>
                        <h6 class="text-uppercase">Address</h6>
                        <p>123 STREET NAME, CITY, US</p>
                    </div>
                    <div>
                        <h6 class="text-uppercase">Phone</h6>
                        <p>(123) 456-7890</p>
                    </div>
                    <div>
                        <h6 class="text-uppercase">Email</h6>
                        <p>example@example.com</p>
                    </div>
                </div>
                <div class="footer-one col-lg-3 col-md-6 col-12">
                    <h5 class="pb-2">Instagram</h5>
                    <div class="row">
                        <img
                            v-for="(image, index) in instagramImages"
                            :key="index"
                            :src="image"
                            class="img-fluid w-25 h-100 m-2"
                            alt=""
                        />
                    </div>
                </div>
            </div>

            <div class="payment-and-social-media container">
                <div class="payment">
                    <img src="/img/payment.png" alt="Payment methods" />
                </div>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fa fa-facebook"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fa fa-twitter"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa fa-linkedin"></i></a>
                </div>
            </div>
        </footer>

        <div class="copyright container">
            <div class="text-center my-3 mx-auto">
                <p>rymo eCommerce @ {{ new Date().getFullYear() }}. All rights reserved</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.shop-main:not(.shop-main--flush) {
    padding-top: 80px;
}
</style>
