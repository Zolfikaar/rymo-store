<script setup lang="ts">
import Footer from '@/components/Footer.vue';
import Header from '@/components/Header.vue';

withDefaults(
    defineProps<{
        flushMain?: boolean;
    }>(),
    {
        flushMain: false,
    },
);
</script>

<template>
    <div class="shop-layout">
        <Header />

        <main class="shop-main" :class="{ 'shop-main--flush': flushMain }">
            <slot />
        </main>

        <Footer />
    </div>
</template>

<style scoped>
.shop-main:not(.shop-main--flush) {
    padding-top: 80px;
}
</style>
