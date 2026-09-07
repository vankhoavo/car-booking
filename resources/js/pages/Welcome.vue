<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { CalendarDays, CarFront, Check, ChevronDown, Clock3, MapPin, Route, ShieldCheck, Sparkles, Users } from 'lucide-vue-next';

const mode = ref<'ride' | 'rental' | 'tour'>('ride');
const tripType = ref<'one-way' | 'round-trip'>('one-way');
const pickup = ref('');
const destination = ref('');
const pickupDate = ref('');
const pickupTime = ref('09:00');
const rentalDays = ref(1);
const passengers = ref(2);

const submitLabel = () => {
    if (mode.value === 'ride') return 'Tìm chuyến xe';
    if (mode.value === 'rental') return 'Tìm xe cho thuê';
    return 'Khám phá tour';
};
</script>

<template>
    <Head title="Đặt xe dễ dàng - Car Booking">
        <meta name="description" content="Đặt xe riêng, thuê xe và đặt xe theo tour nhanh chóng, minh bạch và tiện lợi." />
    </Head>

    <div class="min-h-screen bg-slate-50 text-slate-950">
        <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-2 font-black tracking-tight">
                    <span class="grid size-9 place-items-center rounded-xl bg-slate-950 text-white shadow-sm"><CarFront class="size-5" /></span>
                    <span class="text-lg sm:text-xl">Car<span class="text-indigo-600">Booking</span></span>
                </Link>
                <nav class="hidden items-center gap-7 text-sm font-medium md:flex">
                    <a href="#dat-xe" class="hover:text-indigo-600">Đặt xe</a>
                    <a href="#thue-xe" class="hover:text-indigo-600">Thuê xe</a>
                    <a href="#tour" class="hover:text-indigo-600">Tour</a>
                    <a href="#tai-xuong" class="hover:text-indigo-600">Ứng dụng</a>
                </nav>
                <div class="flex items-center gap-2">
                    <Link href="/login" class="rounded-xl px-3 py-2 text-sm font-semibold hover:bg-slate-100 sm:px-4">Đăng nhập</Link>
                    <Link href="/register" class="rounded-xl bg-slate-950 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 sm:px-4">Đăng ký</Link>
                </div>
            </div>
        </header>

        <main>
            <section class="relative overflow-hidden bg-slate-950">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_18%_25%,rgba(99,102,241,.28),transparent_32%),radial-gradient(circle_at_85%_15%,rgba(14,165,233,.2),transparent_28%)]" />
                <div class="relative mx-auto grid max-w-7xl gap-12 px-4 py-14 sm:px-6 lg:grid-cols-[1.05fr_.95fr] lg:items-center lg:px-8 lg:py-20">
                    <div class="text-white">
                        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-slate-200">
                            <Sparkles class="size-3.5 text-indigo-300" /> Đặt xe nhanh, giá rõ ràng
                        </div>
                        <h1 class="max-w-3xl text-4xl font-black tracking-tight sm:text-5xl lg:text-6xl">Di chuyển theo cách của bạn.</h1>
                        <p class="mt-5 max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">Một nơi cho mọi nhu cầu: đặt xe đi ngay, thuê xe theo ngày hoặc lên lịch xe riêng cho cả hành trình.</p>
                        <div class="mt-8 grid max-w-2xl grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-2xl font-bold">24/7</p><p class="mt-1 text-xs text-slate-400">Hỗ trợ</p></div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-2xl font-bold">3+</p><p class="mt-1 text-xs text-slate-400">Hình thức đặt</p></div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-2xl font-bold">100%</p><p class="mt-1 text-xs text-slate-400">Minh bạch giá</p></div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-2xl font-bold">4.9/5</p><p class="mt-1 text-xs text-slate-400">Đánh giá</p></div>
                        </div>
                    </div>

                    <div id="dat-xe" class="rounded-3xl border border-white/10 bg-white p-3 shadow-2xl shadow-black/30 sm:p-4">
                        <div class="grid grid-cols-3 gap-1 rounded-2xl bg-slate-100 p-1">
                            <button @click="mode = 'ride'" class="rounded-xl px-3 py-3 text-sm font-semibold transition" :class="mode === 'ride' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500'">Đặt xe</button>
                            <button @click="mode = 'rental'" class="rounded-xl px-3 py-3 text-sm font-semibold transition" :class="mode === 'rental' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500'">Thuê xe</button>
                            <button @click="mode = 'tour'" class="rounded-xl px-3 py-3 text-sm font-semibold transition" :class="mode === 'tour' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500'">Theo tour</button>
                        </div>

                        <div class="mt-4 space-y-4">
                            <div v-if="mode === 'ride'" class="space-y-4">
                                <div class="grid grid-cols-2 gap-2 rounded-2xl bg-slate-50 p-1">
                                    <button @click="tripType = 'one-way'" class="rounded-xl px-3 py-2.5 text-sm font-semibold" :class="tripType === 'one-way' ? 'bg-white shadow-sm' : 'text-slate-500'">Một chiều</button>
                                    <button @click="tripType = 'round-trip'" class="rounded-xl px-3 py-2.5 text-sm font-semibold" :class="tripType === 'round-trip' ? 'bg-white shadow-sm' : 'text-slate-500'">Khứ hồi</button>
                                </div>
                            </div>

                            <div v-if="mode !== 'tour'" class="grid gap-3 sm:grid-cols-2">
                                <label class="block sm:col-span-2">
                                    <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Điểm đón</span>
                                    <div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100">
                                        <MapPin class="size-5 shrink-0 text-indigo-600" />
                                        <input v-model="pickup" class="w-full border-0 p-0 text-sm outline-none focus:ring-0" placeholder="Nhập địa điểm đón" />
                                    </div>
                                </label>
                                <label class="block sm:col-span-2">
                                    <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Điểm đến</span>
                                    <div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100">
                                        <Route class="size-5 shrink-0 text-indigo-600" />
                                        <input v-model="destination" class="w-full border-0 p-0 text-sm outline-none focus:ring-0" placeholder="Bạn muốn đi đâu?" />
                                    </div>
                                </label>
                            </div>

                            <div v-if="mode === 'tour'" class="grid gap-3 sm:grid-cols-2">
                                <label class="block sm:col-span-2">
                                    <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Điểm khởi hành</span>
                                    <div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3"><MapPin class="size-5 text-indigo-600" /><input v-model="pickup" class="w-full border-0 p-0 text-sm outline-none focus:ring-0" placeholder="Hà Nội, Đà Nẵng, TP. Hồ Chí Minh..." /></div>
                                </label>
                                <label class="block sm:col-span-2">
                                    <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Bạn muốn khám phá</span>
                                    <div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3"><Route class="size-5 text-indigo-600" /><input v-model="destination" class="w-full border-0 p-0 text-sm outline-none focus:ring-0" placeholder="Hạ Long, Sapa, Hội An..." /></div>
                                </label>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                <label class="block">
                                    <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Ngày đi</span>
                                    <div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3"><CalendarDays class="size-5 text-indigo-600" /><input v-model="pickupDate" type="date" class="w-full border-0 p-0 text-sm outline-none focus:ring-0" /></div>
                                </label>
                                <label v-if="mode !== 'tour'" class="block">
                                    <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Giờ</span>
                                    <div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3"><Clock3 class="size-5 text-indigo-600" /><input v-model="pickupTime" type="time" class="w-full border-0 p-0 text-sm outline-none focus:ring-0" /></div>
                                </label>
                                <label class="block">
                                    <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Hành khách</span>
                                    <div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3"><Users class="size-5 text-indigo-600" /><select v-model="passengers" class="w-full border-0 bg-transparent p-0 text-sm outline-none focus:ring-0"><option v-for="n in 8" :key="n" :value="n">{{ n }} người</option></select><ChevronDown class="size-4 text-slate-400" /></div>
                                </label>
                            </div>

                            <div v-if="mode === 'rental'" class="rounded-2xl bg-indigo-50 p-4">
                                <div class="flex items-center justify-between"><div><p class="font-semibold">Thuê theo ngày</p><p class="text-xs text-slate-500">Linh hoạt đổi số ngày trước khi thanh toán</p></div><div class="flex items-center gap-2"><button @click="rentalDays = Math.max(1, rentalDays - 1)" class="grid size-9 place-items-center rounded-xl bg-white text-lg shadow-sm">−</button><span class="w-8 text-center font-bold">{{ rentalDays }}</span><button @click="rentalDays++" class="grid size-9 place-items-center rounded-xl bg-white text-lg shadow-sm">+</button></div></div>
                            </div>

                            <button class="w-full rounded-2xl bg-indigo-600 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-500">{{ submitLabel() }}</button>
                            <p class="text-center text-xs text-slate-400">Chưa tính phí phát sinh · Xác nhận giá trước khi thanh toán</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="thue-xe" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div><p class="text-sm font-bold uppercase tracking-widest text-indigo-600">Thuê xe</p><h2 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Chọn chiếc xe phù hợp chuyến đi</h2><p class="mt-3 max-w-2xl text-slate-500">Từ xe gia đình đến xe cao cấp, tập trung vào không gian, tiện nghi và mức giá dễ hiểu.</p></div>
                    <a href="#" class="text-sm font-bold text-indigo-600 hover:text-indigo-500">Xem tất cả →</a>
                </div>
                <div class="mt-8 grid gap-5 md:grid-cols-3">
                    <article v-for="car in [
                        {name:'Toyota Innova', seats:'7 chỗ', fuel:'Xăng', price:'900.000đ/ngày', tag:'Gia đình'},
                        {name:'Kia Carnival', seats:'7 chỗ', fuel:'Dầu', price:'1.450.000đ/ngày', tag:'Cao cấp'},
                        {name:'VinFast VF 9', seats:'7 chỗ', fuel:'Điện', price:'1.800.000đ/ngày', tag:'Tiết kiệm'}
                    ]" :key="car.name" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="aspect-[16/10] bg-gradient-to-br from-slate-100 via-slate-200 to-slate-300 p-5"><div class="flex items-start justify-between"><span class="rounded-full bg-white/85 px-3 py-1 text-xs font-bold">{{ car.tag }}</span><CarFront class="size-6 text-slate-500" /></div><div class="flex h-full items-end"><div><p class="text-2xl font-black tracking-tight text-slate-700">{{ car.name }}</p><p class="text-sm text-slate-500">{{ car.seats }} · {{ car.fuel }}</p></div></div></div>
                        <div class="p-5"><div class="flex items-end justify-between gap-4"><div><p class="text-xs text-slate-400">Từ</p><p class="text-lg font-black">{{ car.price }}</p></div><button class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-bold text-white">Chọn xe</button></div></div>
                    </article>
                </div>
            </section>

            <section id="tour" class="border-y border-slate-200 bg-white">
                <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[.9fr_1.1fr] lg:px-8">
                    <div><p class="text-sm font-bold uppercase tracking-widest text-indigo-600">Đặt xe theo tour</p><h2 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Không chỉ là chuyến xe, mà là cả hành trình.</h2><p class="mt-4 text-slate-500">Chọn lịch trình có sẵn hoặc đặt tour riêng. Giá hiển thị theo hành trình để bạn dễ dự trù chi phí.</p><div class="mt-7 grid gap-3 sm:grid-cols-2"><div class="rounded-2xl bg-slate-50 p-4"><ShieldCheck class="size-5 text-indigo-600" /><p class="mt-3 font-bold">Tài xế đồng hành</p><p class="mt-1 text-sm text-slate-500">Theo sát lịch trình, hỗ trợ điểm dừng.</p></div><div class="rounded-2xl bg-slate-50 p-4"><Route class="size-5 text-indigo-600" /><p class="mt-3 font-bold">Lịch trình linh hoạt</p><p class="mt-1 text-sm text-slate-500">Thay đổi điểm đến trong phạm vi tour.</p></div></div></div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <article v-for="tour in [{name:'Đà Nẵng – Hội An – Bà Nà',days:'2N1Đ',price:'Từ 2.490.000đ',image:'ĐÀ NẴNG'}, {name:'Hà Nội – Ninh Bình – Hạ Long',days:'3N2Đ',price:'Từ 3.990.000đ',image:'MIỀN BẮC'}, {name:'Đà Lạt – Tà Nung – Mê Linh',days:'2N1Đ',price:'Từ 2.290.000đ',image:'ĐÀ LẠT'}, {name:'Sài Gòn – Vũng Tàu – Hồ Tràm',days:'2N1Đ',price:'Từ 2.190.000đ',image:'BIỂN'}]" :key="tour.name" class="group overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 transition hover:shadow-md"><div class="flex h-32 items-end bg-gradient-to-br from-indigo-100 via-sky-100 to-slate-200 p-4"><span class="rounded-full bg-white/80 px-3 py-1 text-xs font-black tracking-widest">{{ tour.image }}</span></div><div class="p-5"><div class="flex items-center justify-between text-xs font-semibold text-slate-400"><span>{{ tour.days }}</span><span class="text-indigo-600">{{ tour.price }}</span></div><h3 class="mt-2 font-black">{{ tour.name }}</h3><button class="mt-4 text-sm font-bold text-slate-900 group-hover:text-indigo-600">Xem tour →</button></div></article>
                    </div>
                </div>
            </section>

            <section id="tai-xuong" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="rounded-[2rem] bg-slate-950 px-6 py-10 text-white sm:px-10 lg:flex lg:items-center lg:justify-between lg:px-12"><div><p class="text-sm font-bold uppercase tracking-widest text-indigo-300">An tâm trước chuyến đi</p><h2 class="mt-2 text-3xl font-black">Thông tin rõ. Đặt xe nhanh. Hỗ trợ khi cần.</h2><p class="mt-3 max-w-2xl text-slate-400">Luôn biết mình đang đặt gì, trả bao nhiêu và hành trình diễn ra như thế nào.</p></div><div class="mt-7 grid gap-3 sm:grid-cols-3 lg:mt-0"><div v-for="item in ['Giá trước khi đặt','Xác nhận tức thì','Hỗ trợ 24/7']" :key="item" class="flex items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold"><Check class="size-4 text-indigo-300" />{{ item }}</div></div></div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 text-sm text-slate-500 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8"><p>© 2026 CarBooking. Đặt xe đơn giản hơn mỗi ngày.</p><div class="flex gap-5"><a href="#" class="hover:text-slate-950">Điều khoản</a><a href="#" class="hover:text-slate-950">Chính sách</a><a href="#" class="hover:text-slate-950">Liên hệ</a></div></div>
        </footer>
    </div>
</template>
