<script setup lang="ts">
import type { Product } from '@/types/shop';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    product: Product;
    navigateOnClick?: boolean;
}>();

function handleCardClick(): void {
    if (!props.navigateOnClick || !props.product.slug) {
        return;
    }

    router.visit(route('shop.product', props.product.slug));
}
</script>

<template>
    <div
        class="product text-center col-lg-3 col-md-4 col-12"
        :class="{ 'product--clickable': navigateOnClick }"
        @click="handleCardClick"
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
</style>
