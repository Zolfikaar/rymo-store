<script setup lang="ts">
import type { Product } from '@/types/shop';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    product: Product;
}>();

function navigateToProduct(): void {
    router.visit(route('shop.product', props.product.slug));
}

function handleCardClick(): void {
    navigateToProduct();
}

function handleBuyNow(): void {
    navigateToProduct();
}
</script>

<template>
    <div
        class="product product--clickable text-center col-lg-3 col-md-4 col-12"
        @click="handleCardClick"
    >
        <img
            :src="product.image"
            :alt="product.name"
            class="product__image img-fluid mb-3 w-full h-64 object-cover"
        />
        <div class="star">
            <i v-for="star in product.rating" :key="star" class="fa fa-star" aria-hidden="true"></i>
        </div>
        <h5 class="p-name">{{ product.name }}</h5>
        <h4 class="p-price">{{ product.price }}</h4>
        <button class="buy-btn" type="button" @click.stop="handleBuyNow">Buy Now</button>
    </div>
</template>

<style scoped>
.product--clickable {
    cursor: pointer;
}

.product {
    cursor: pointer;
    margin-bottom: 2rem;
}

.product__image {
    width: 100%;
    height: 16rem;
    object-fit: cover;
}

.product img {
    transition: 0.3s all;
}

.product:hover img {
    opacity: 0.7;
}
.product .p-name,
.product .p-price {
    margin-bottom: 0.5rem;
}
.product .buy-btn {
    background: #fb774b;
    transform: translateY(20px);
    opacity: 0;
    transition: 0.3s all;
}

.product:hover .buy-btn {
    opacity: 1;
    transform: translateY(0);
}
</style>
