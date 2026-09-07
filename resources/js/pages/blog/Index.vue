<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, BookOpen, CarFront } from '@lucide/vue';

interface Post { id:number; title:string; slug:string; excerpt:string|null; image:string|null; published_at:string|null; }
defineProps<{ posts: Post[] }>();
</script>

<template>
<Head title="Blog" />
<div class="min-h-screen bg-slate-50 text-slate-950">
<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur"><div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8"><Link href="/" class="flex items-center gap-2 font-black"><span class="grid size-9 place-items-center rounded-xl bg-slate-950 text-white"><CarFront class="size-5" /></span><span>Car<span class="text-indigo-600">Booking</span></span></Link><nav class="hidden items-center gap-6 text-sm font-semibold md:flex"><Link href="/dat-xe" class="hover:text-indigo-600">Đặt xe</Link><Link href="/thue-xe" class="hover:text-indigo-600">Thuê xe</Link><a href="/tour" class="hover:text-indigo-600">Theo tour</a><Link href="/blog" class="text-indigo-600">Blog</Link></nav><Link href="/" class="text-sm font-semibold text-slate-500">Trang chủ</Link></div></header>
<main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8"><div class="mb-8"><div class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700"><BookOpen class="size-4" /> Kiến thức di chuyển</div><h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Blog CarBooking</h1><p class="mt-2 max-w-2xl text-slate-500">Kinh nghiệm đặt xe, thuê xe và chuẩn bị hành trình thuận tiện hơn.</p></div><div v-if="posts.length" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"><article v-for="post in posts" :key="post.id" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg"><div class="aspect-[16/9] bg-slate-100"><img v-if="post.image" :src="post.image" :alt="post.title" class="h-full w-full object-cover" /><div v-else class="grid h-full place-items-center text-slate-300"><BookOpen class="size-14" /></div></div><div class="p-5"><p class="text-xs font-semibold text-slate-400">{{ post.published_at || 'Mới cập nhật' }}</p><h2 class="mt-2 text-xl font-black leading-7">{{ post.title }}</h2><p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-500">{{ post.excerpt || 'Khám phá thêm những thông tin hữu ích cho hành trình của bạn.' }}</p><Link :href="`/blog/${post.slug}`" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-indigo-600">Đọc bài viết <ArrowRight class="size-4" /></Link></div></article></div><div v-else class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center"><BookOpen class="mx-auto size-10 text-slate-300" /><h2 class="mt-4 text-xl font-black">Chưa có bài viết</h2><p class="mt-2 text-sm text-slate-500">Nội dung sẽ được cập nhật sớm.</p></div></main>
</div>
</template>
