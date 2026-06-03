<script setup lang="ts">
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { BlogPost } from '@/types/blog';
import { Head } from '@inertiajs/vue3';

defineProps<{
    posts: BlogPost[];
}>();

function postColumnClass(post: BlogPost): string {
    if (post.size === 'banner') {
        return 'col-lg-12 col-md-12 col-12 pb-5';
    }

    if (post.size === 'small') {
        return 'post col-lg-4 col-md-6 col-12 pb-5';
    }

    return 'post col-lg-6 col-md-6 col-12';
}
</script>

<template>
    <Head title="Blog" />

    <ShopLayout>
        <section id="blog-home" class="container pt-5 mt-5">
            <h2 class="font-weight-bold pt-5">Blogs</h2>
            <hr />
        </section>

        <section id="blog-container" class="container pt-5">
            <div class="row">
                <div
                    v-for="post in posts"
                    :key="post.id"
                    :class="postColumnClass(post)"
                >
                    <div class="post-img">
                        <img class="img-fluid w-100" :src="post.image" :alt="post.title" />
                    </div>
                    <h3
                        v-if="post.size === 'large'"
                        class="post-title text-center font-weight-normal pt-3"
                    >
                        {{ post.title }}
                    </h3>
                    <h4
                        v-else-if="post.size === 'small'"
                        class="post-title font-weight-normal pt-3"
                    >
                        {{ post.title }}
                    </h4>
                    <p v-if="post.date" class="post-date text-center">{{ post.date }}</p>
                </div>
            </div>
        </section>
    </ShopLayout>
</template>

<style scoped>
#blog-container .post .post-img {
    overflow: hidden;
    cursor: pointer;
}

#blog-container .post img {
    transition: .3s ease;
}

#blog-container .post:hover img {
    transform: scale(1.1);
    opacity: .9;
}

#blog-container .post h3 {
    transition: .3s ease;
    cursor: pointer;
}

#blog-container .post:hover h3 {
    color: #fd8c66
}
</style>
