<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { FileText, Plus, Pencil, Trash2, X } from 'lucide-vue-next';
import { ref } from 'vue';

type Post = { id: number; title: string; slug: string; excerpt: string | null; content: string; image: string | null; published_at: string | null; is_published: boolean };
const props = defineProps<{ posts: Post[] }>();
const empty = (): Post => ({ id: 0, title: '', slug: '', excerpt: '', content: '', image: '', published_at: '', is_published: false });
const editing = ref<Post | null>(null);
const form = ref<Post>(empty());
const isFormOpen = ref(false);
const openCreate = () => { editing.value = null; form.value = empty(); isFormOpen.value = true; };
const openEdit = (post: Post) => { editing.value = post; form.value = { ...post }; isFormOpen.value = true; };
const close = () => { editing.value = null; isFormOpen.value = false; };
const save = () => {
    const data = { title: form.value.title, slug: form.value.slug, excerpt: form.value.excerpt, content: form.value.content, image: form.value.image || null, published_at: form.value.published_at || null, is_published: form.value.is_published };
    if (editing.value) router.put(`/admin/blog/${editing.value.id}`, data, { preserveScroll: true, onSuccess: close });
    else router.post('/admin/blog', data, { preserveScroll: true, onSuccess: close });
};
const remove = (post: Post) => { if (window.confirm(`Xóa bài viết “${post.title}”?`)) router.delete(`/admin/blog/${post.id}`, { preserveScroll: true }); };
const toggle = (post: Post) => router.put(`/admin/blog/${post.id}`, { title: post.title, slug: post.slug, excerpt: post.excerpt, content: post.content, image: post.image, published_at: post.published_at, is_published: !post.is_published }, { preserveScroll: true });
const date = (value: string | null) => value ? new Date(value).toLocaleDateString('vi-VN') : 'Chưa xuất bản';
</script>

<template>
    <Head title="Quản lý Blog" />
    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"><div><h1 class="text-2xl font-semibold tracking-tight">Quản lý Blog</h1><p class="mt-1 text-sm text-muted-foreground">Tạo, chỉnh sửa và xuất bản nội dung website.</p></div><button class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground" @click="openCreate"><Plus class="size-4" />Bài viết mới</button></div>
        <section class="overflow-hidden rounded-xl border bg-card shadow-sm"><div class="overflow-x-auto"><table class="w-full min-w-[900px] text-left text-sm"><thead class="bg-muted/40 text-muted-foreground"><tr><th class="px-5 py-3 font-medium">Tiêu đề</th><th class="px-5 py-3 font-medium">Slug</th><th class="px-5 py-3 font-medium">Ngày đăng</th><th class="px-5 py-3 font-medium">Trạng thái</th><th class="px-5 py-3 text-right font-medium">Thao tác</th></tr></thead><tbody><tr v-for="post in props.posts" :key="post.id" class="border-t"><td class="px-5 py-4"><div class="font-medium">{{ post.title }}</div><div v-if="post.excerpt" class="mt-1 max-w-md truncate text-muted-foreground">{{ post.excerpt }}</div></td><td class="px-5 py-4 text-muted-foreground">{{ post.slug }}</td><td class="whitespace-nowrap px-5 py-4">{{ date(post.published_at) }}</td><td class="px-5 py-4"><button class="rounded-full px-2.5 py-1 text-xs font-medium" :class="post.is_published ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-muted text-muted-foreground'" @click="toggle(post)">{{ post.is_published ? 'Đang xuất bản' : 'Bản nháp' }}</button></td><td class="px-5 py-4"><div class="flex justify-end gap-2"><button class="rounded-md border p-2 hover:bg-muted" title="Sửa" @click="openEdit(post)"><Pencil class="size-4" /></button><button class="rounded-md border p-2 text-destructive hover:bg-muted" title="Xóa" @click="remove(post)"><Trash2 class="size-4" /></button></div></td></tr><tr v-if="props.posts.length === 0"><td colspan="5" class="px-5 py-12 text-center text-muted-foreground"><FileText class="mx-auto mb-2 size-8" />Chưa có bài viết.</td></tr></tbody></table></div></section>
        <div v-if="isFormOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @click.self="close"><div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-xl border bg-card p-6 shadow-xl"><div class="flex items-start justify-between"><div><h2 class="text-lg font-semibold">{{ editing ? 'Chỉnh sửa bài viết' : 'Bài viết mới' }}</h2><p class="text-sm text-muted-foreground">Nội dung sẽ được lưu trực tiếp vào hệ thống Blog.</p></div><button class="rounded-md p-2 hover:bg-muted" @click="close"><X class="size-5" /></button></div><div class="mt-5 grid gap-4"><label class="grid gap-1.5 text-sm">Tiêu đề<input v-model="form.title" class="rounded-lg border bg-background px-3 py-2" /></label><label class="grid gap-1.5 text-sm">Slug<input v-model="form.slug" placeholder="Tự động tạo nếu để trống" class="rounded-lg border bg-background px-3 py-2" /></label><label class="grid gap-1.5 text-sm">Mô tả ngắn<textarea v-model="form.excerpt" rows="2" class="rounded-lg border bg-background px-3 py-2" /></label><label class="grid gap-1.5 text-sm">Nội dung<textarea v-model="form.content" rows="10" class="rounded-lg border bg-background px-3 py-2" /></label><label class="grid gap-1.5 text-sm">Ảnh đại diện (URL)<input v-model="form.image" type="url" class="rounded-lg border bg-background px-3 py-2" /></label><div class="flex flex-wrap gap-4"><label class="grid flex-1 gap-1.5 text-sm">Ngày đăng<input v-model="form.published_at" type="date" class="rounded-lg border bg-background px-3 py-2" /></label><label class="flex items-end gap-2 pb-2 text-sm"><input v-model="form.is_published" type="checkbox" class="size-4" /> Xuất bản bài viết</label></div></div><div class="mt-6 flex justify-end gap-2"><button class="rounded-lg border px-4 py-2 text-sm" @click="close">Hủy</button><button class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground" @click="save">Lưu bài viết</button></div></div></div>
    </div>
</template>
