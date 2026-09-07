<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, CarFront } from '@lucide/vue';
interface Post { title:string; excerpt:string|null; content:string; image:string|null; published_at:string|null; }
defineProps<{ post: Post }>();
</script>
<template>
<Head :title="post.title" />
<div class="min-h-screen bg-slate-50 text-slate-950">
<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur"><div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8"><Link href="/" class="flex items-center gap-2 font-black"><span class="grid size-9 place-items-center rounded-xl bg-slate-950 text-white"><CarFront class="size-5" /></span><span>Car<span class="text-indigo-600">Booking</span></span></Link><Link href="/blog" class="text-sm font-semibold text-slate-600 hover:text-slate-950"><ArrowLeft class="mr-1 inline size-4" /> Blog</Link></div></header>
<main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8"><p class="text-sm font-bold text-indigo-600">{{ post.published_at || 'Mới cập nhật' }}</p><h1 class="mt-2 text-3xl font-black tracking-tight sm:text-5xl">{{ post.title }}</h1><p v-if="post.excerpt" class="mt-4 text-lg leading-8 text-slate-500">{{ post.excerpt }}</p><div v-if="post.image" class="mt-8 overflow-hidden rounded-3xl bg-slate-100"><img :src="post.image" :alt="post.title" class="max-h-[520px] w-full object-cover" /></div><article class="prose prose-slate mt-8 max-w-none whitespace-pre-line leading-8"><div>{{ post.content }}</div></article></main>
</div>
</template>
