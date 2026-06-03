<script setup lang="ts">
import ProductCard from '@/components/ProductCard.vue';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Product } from '@/types/shop';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    featuredProducts: Product[];
    clothesProducts: Product[];
    watchesProducts: Product[];
    shoesProducts: Product[];
}>();

const brandImages = [1, 2, 3, 4, 5, 6].map((n) => `/img/brand/${n}.png`);

const newArrivals = [
    { image: '/img/new/1.jpg', title: 'Extreme Rare Sneakers', container: true },
    { image: '/img/new/2.jpg', title: 'Awsome Blank Outfit', container: false },
    { image: '/img/new/3.jpg', title: 'Sportwear Up To 50% Off', container: true },
];
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
                        :key="product.slug"
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
                        :key="product.slug"
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
                        :key="product.slug"
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
                        :key="product.slug"
                        :product="product"
                    />
                </div>
            </div>
        </section>
    </ShopLayout>
</template>

<style scoped>
#hero {
    background-image: url('/img/back.jpg');
    width: 100%;
    height: 100vh;
    background-size: cover;
    background-position: top 60px center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
}

#hero span {
    color: coral;
}

#new .one img {
    width: 100%;
    height: 100%;
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
    position: relative;
}

#new .one .details {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    transition: .3s ease;
}

#new .one:hover .details {
    cursor: pointer;
    background-color: rgba(245, 178, 178, .7);
}

#new .one .details button {
    display: inline-block;
    font-size: 14px;
    font-weight: 500;
    color: #2a2a2a;
    background: none;
    text-transform: uppercase;
    border-bottom: 1px solid #2a2a2a;
    padding: 2.5px;
    transform: translateY(70px);
    transition: .3s ease;
}

#new .one .details button:hover {
    color: #fff;
    border-bottom: 1px solid #fff;
}

#new .one:nth-child(1) .details,
#new .one:nth-child(2) .details,
#new .one:nth-child(3) .details {
    display: flex;
    justify-content: center;
    flex-direction: column;
}

#new .one:nth-child(1) .details {
    align-items: flex-start;
    text-align: start;
}

#new .one:nth-child(2) .details {
    align-items: center;
    text-align: center;
}

#new .one:nth-child(3) .details {
    align-items: flex-end;
    text-align: end;
}

#banner {
    background-image: url('/img/banner/2.jpg');
    width: 100%;
    height: 60vh;
    background-size: cover;
    background-position: top 70px center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
}

#banner h4 {
    color: #d8d8d8;
}

#banner h1 {
    color: #fff;
}

#banner button {
    background-color: #fb774b;
}
</style>
