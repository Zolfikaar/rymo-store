<script setup lang="ts">
import ProductCard from '@/components/ProductCard.vue';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Product, ProductDetail } from '@/types/shop';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    product: ProductDetail;
    relatedProducts: Product[];
}>();

const mainImage = ref(props.product.images[0] ?? '');
const selectedSize = ref('');
const quantity = ref(1);

const showGalleryThumbnails = computed(() => props.product.images.length > 1);
const showSizeSelector = computed(() => props.product.sizes.length > 0);

function setMainImage(image: string): void {
    mainImage.value = image;
}
</script>

<template>

    <Head :title="product.name" />

    <ShopLayout>
        <section class="container s-product my-5 pt-5">
            <div class="row mt-5">
                <div class="col-lg-6 col-md-12 col-12">
                    <img :src="mainImage" class="img-fluid w-100 pb-1" alt="Product image" />

                    <div v-if="showGalleryThumbnails" class="small-img-group">
                        <div v-for="(image, index) in product.images" :key="index" class="small-img-col"
                            @click="setMainImage(image)">
                            <img :src="image" width="100%" class="small-img" alt="" />
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-12">
                    <h6 class="category-name">{{ product.category }}</h6>
                    <h3 class="py-4">{{ product.name }}</h3>
                    <h2>{{ product.price }}</h2>
                    <select v-if="showSizeSelector" v-model="selectedSize">
                        <option value="">Select Size</option>
                        <option v-for="size in product.sizes" :key="size" :value="size">
                            {{ size }}
                        </option>
                    </select>
                    <input v-model.number="quantity" type="number" min="1" value="1" />
                    <button type="button" class="buy-btn my-2">Add To Cart</button>
                    <h4 class="my-5">Product Details</h4>
                    <p>{{ product.description }}</p>
                </div>
            </div>
        </section>

        <section id="featured" class="my-5 pb-5">
            <div class="container">
                <div class="text-center mt-5 py-5">
                    <h3>Related Products</h3>
                    <hr class="mx-auto" />
                </div>
                <div class="row mx-auto">
                    <ProductCard v-for="related in relatedProducts" :key="related.slug" :product="related" />
                </div>
            </div>
        </section>
    </ShopLayout>
</template>

<style scoped>
.s-product .small-img-group {
    display: flex;
    justify-content: space-between;
}

.s-product .small-img-col {
    flex-basis: 24%;
    cursor: pointer;
}

.s-product select {
    display: block;
    padding: 5px 10px;
}

.s-product input[type='number'] {
    width: 50px;
    height: 40px;
    padding-left: 10px;
    font-size: 16px;
    margin-right: 10px;
}

.s-product input[type='number']:focus {
    outline: none;
}

.s-product .buy-btn {
    background-color: #fb774b;
    opacity: 1;
    transition: 0.3s all;
}

.s-product .category-name {
    margin-top: 2rem;
    margin-bottom: 0;
}
</style>
