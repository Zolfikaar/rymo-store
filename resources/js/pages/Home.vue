<script setup lang="ts">
import ProductCard from '@/components/ProductCard.vue';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Product } from '@/types/shop';
import { Head, Link } from '@inertiajs/vue3';

function makeProducts(folder: string, images: string[], startId = 1): Product[] {
    return images.map((image, index) => ({
        id: startId + index,
        name: 'Sport Boots',
        price: '$92.00',
        image: `/img/${folder}/${image}`,
        rating: 5,
    }));
}

const brandImages = [1, 2, 3, 4, 5, 6].map((n) => `/img/brand/${n}.png`);

const newArrivals = [
    { image: '/img/new/1.jpg', title: 'Extreme Rare Sneakers', container: true },
    { image: '/img/new/2.jpg', title: 'Awsome Blank Outfit', container: false },
    { image: '/img/new/3.jpg', title: 'Sportwear Up To 50% Off', container: true },
];

const featuredProducts = makeProducts('featured', ['1.jpg', '2.jpg', '3.jpg', '4.jpg']);
const clothesProducts = makeProducts('clothes', ['1.jpg', '2.jpg', '3.jpg', '1.jpg'], 10);
const watchesProducts = makeProducts('watches', ['1.jpg', '2.jpg', '3.jpg', '4.jpg'], 20);
const shoesProducts = makeProducts('shoes', ['1.jpg', '2.jpg', '3.jpg', '4.jpg'], 30);
</script>

<template>
    <Head title="Home" />

    <ShopLayout flush-main>
        <section id="hero">
            <div class="container">
                <div class="new-arraival">
                    <h5 class="section-header">NEW ARRAIVALS</h5>
                    <h1><span>Best Price</span>This Year</h1>
                    <p>
                        Shoomatic offers you very comfortable time <br />
                        on walking and exercises.
                    </p>
                    <Link :href="route('shop')">
                        <button type="button">Shop Now</button>
                    </Link>
                </div>
            </div>
        </section>

        <section id="brands" class="container">
            <div class="row m-0 py-5">
                <img
                    v-for="(src, index) in brandImages"
                    :key="index"
                    class="img-fluid col-lg-2 col-md-4 col-6"
                    :src="src"
                    alt=""
                />
            </div>
        </section>

        <section id="new" class="w-100">
            <div class="row p-0 m-0">
                <div
                    v-for="(item, index) in newArrivals"
                    :key="index"
                    class="one col-lg-4 col-md-12 col-12 p-0"
                >
                    <img class="img-fluid" :src="item.image" alt="" />
                    <div class="details" :class="{ container: item.container }">
                        <h2>{{ item.title }}</h2>
                        <Link :href="route('shop')">
                            <button type="button" class="text-uppercase">Shop Now</button>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <section id="featured" class="my-5 pb-5">
            <div class="container">
                <div class="text-center mt-5 py-5">
                    <h3>Our Featured</h3>
                    <hr class="mx-auto" />
                    <p>Here you can check out our new products with fair price on rymo.</p>
                </div>
                <div class="row mx-auto">
                    <ProductCard
                        v-for="product in featuredProducts"
                        :key="product.id"
                        :product="product"
                    />
                </div>
            </div>
        </section>

        <section id="banner" class="my-5 py-5">
            <div class="container">
                <h4>MID SEASON'S SALE</h4>
                <h1>Autumn Collection<br />Up To 20% Off</h1>
                <Link :href="route('shop')">
                    <button type="button" class="text-uppercase">Shop Now</button>
                </Link>
            </div>
        </section>

        <section id="clothes" class="my-5">
            <div class="container">
                <div class="text-center mt-5 py-5">
                    <h3>Dresses & Jumpsuits</h3>
                    <hr class="mx-auto" />
                    <p>Here you can check out our new products with fair price on rymo.</p>
                </div>
                <div class="row mx-auto">
                    <ProductCard
                        v-for="product in clothesProducts"
                        :key="`clothes-${product.id}`"
                        :product="product"
                    />
                </div>
            </div>
        </section>

        <section id="watches" class="my-5">
            <div class="container">
                <div class="text-center mt-5 py-5">
                    <h3>Best Watches</h3>
                    <hr class="mx-auto" />
                    <p>Here you can check out our new products with fair price on rymo.</p>
                </div>
                <div class="row mx-auto">
                    <ProductCard
                        v-for="product in watchesProducts"
                        :key="`watches-${product.id}`"
                        :product="product"
                    />
                </div>
            </div>
        </section>

        <section id="shoes" class="my-5 pb-5">
            <div class="container">
                <div class="text-center mt-5 py-5">
                    <h3>Running Shoes</h3>
                    <hr class="mx-auto" />
                    <p>Here you can check out our new products with fair price on rymo.</p>
                </div>
                <div class="row mx-auto">
                    <ProductCard
                        v-for="product in shoesProducts"
                        :key="`shoes-${product.id}`"
                        :product="product"
                    />
                </div>
            </div>
        </section>
    </ShopLayout>
</template>
