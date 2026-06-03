<script setup lang="ts">
import type { Product } from '@/types/shop';
import { router } from '@inertiajs/vue3';

defineProps<{
    product: Product;
}>();

function handleCardClick(slug: string): void {
    router.visit(route('shop.product', slug));
}
</script>

<template>
    <div
        class="product product--clickable text-center col-lg-3 col-md-4 col-12"
        @click="handleCardClick(product.slug)"
    >
        <img :src="product.image" :alt="product.name" class="img-fluid mb-3" />
        <div class="star">
            <i v-for="star in product.rating" :key="star" class="fa fa-star" aria-hidden="true"></i>
        </div>
        <h5 class="p-name">{{ product.name }}</h5>
        <h4 class="p-price">{{ product.price }}</h4>
        <button class="buy-btn" type="button" @click.stop>Buy Now</button>
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

.product img {
    transition: .3s all;
}

.product:hover img {
    opacity: .7;
}

.product .buy-btn {
    background: #fb774b;
    transform: translateY(20px);
    opacity: 0;
    transition: .3s all;
}

.product:hover .buy-btn {
    opacity: 1;
    transform: translateY(0);
}
</style>
