<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CalendarDays, CarFront, Check, ChevronRight, MapPin, Route, Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const selected = ref('Đà Nẵng – Hội An – Bà Nà');
const guests = ref(4);
const tourDate = ref('');
const pickup = ref('');
const tours = [
    { name: 'Đà Nẵng – Hội An – Bà Nà', days: '2N1Đ', route: 'Đà Nẵng · Hội An · Bà Nà Hills', destination: 'Đà Nẵng – Hội An – Bà Nà', price: '2.490.000đ', desc: 'Phù hợp nhóm gia đình và bạn bè.' },
    { name: 'Hà Nội – Ninh Bình – Hạ Long', days: '3N2Đ', route: 'Hà Nội · Ninh Bình · Hạ Long', destination: 'Hà Nội – Ninh Bình – Hạ Long', price: '3.990.000đ', desc: 'Khám phá thiên nhiên và di sản miền Bắc.' },
    { name: 'Đà Lạt – Tà Nung – Mê Linh', days: '2N1Đ', route: 'Đà Lạt · Tà Nung · Mê Linh', destination: 'Đà Lạt – Tà Nung – Mê Linh', price: '2.290.000đ', desc: 'Nhẹ nhàng, nhiều điểm check-in.' },
    { name: 'Sài Gòn – Vũng Tàu – Hồ Tràm', days: '2N1Đ', route: 'TP. Hồ Chí Minh · Vũng Tàu · Hồ Tràm', destination: 'Sài Gòn – Vũng Tàu – Hồ Tràm', price: '2.190.000đ', desc: 'Đi biển cuối tuần, tối ưu thời gian.' },
];
const selectedTour = computed(() => tours.find((tour) => tour.name === selected.value) ?? tours[0]);
const bookingUrl = computed(() => {
    const params = new URLSearchParams({ destination: selectedTour.value.destination, passengers: String(guests.value) });
    if (tourDate.value) params.set('date', tourDate.value);
    if (pickup.value.trim()) params.set('pickup', pickup.value.trim());
    return `/dat-xe?${params.toString()}`;
});
const minDate = new Date().toLocaleDateString('en-CA');
</script>

<template>
    <Head title="Đặt xe theo tour" />
    <div class="min-h-screen bg-slate-50 text-slate-950">
        <header class="border-b border-slate-200 bg-white"><div class="mx-auto flex min-h-16 max-w-7xl flex-wrap items-center gap-3 px-4 py-3 sm:px-6 lg:px-8"><Link href="/" class="flex items-center gap-2"><span class="grid size-9 shrink-0 place-items-center rounded-xl bg-slate-950 text-white"><CarFront class="size-5" /></span><span class="font-black">CarBooking</span></Link><span class="mx-2 hidden text-slate-300 sm:block">/</span><span class="text-sm font-semibold text-slate-500">Đặt xe theo tour</span></div></header>
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8"><p class="text-sm font-bold uppercase tracking-widest text-indigo-600">Hành trình trọn gói</p><h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Chọn tour, chúng tôi lo phần xe.</h1><p class="mt-2 max-w-2xl text-slate-500">Lịch trình rõ ràng, xe riêng, tài xế đồng hành và mức giá theo nhóm.</p></div>
            <div class="grid gap-6 lg:grid-cols-[1fr_340px]">
                <section class="space-y-4">
                    <button v-for="tour in tours" :key="tour.name" @click="selected = tour.name" class="w-full overflow-hidden rounded-3xl border bg-white p-4 text-left shadow-sm transition sm:p-5" :class="selected === tour.name ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-slate-200 hover:border-slate-300'"><div class="flex flex-col gap-4 sm:flex-row"><div class="flex aspect-[16/10] w-full shrink-0 items-end rounded-2xl bg-gradient-to-br from-indigo-100 via-sky-100 to-slate-200 p-4 sm:w-56"><span class="rounded-full bg-white/80 px-3 py-1 text-xs font-black">{{ tour.days }}</span></div><div class="min-w-0 flex-1"><div class="flex items-start justify-between gap-3"><div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><h2 class="break-words text-lg font-black sm:text-xl">{{ tour.name }}</h2><span v-if="selected === tour.name" class="rounded-full bg-indigo-600 p-1 text-white"><Check class="size-3" /></span></div><p class="mt-2 inline-flex max-w-full items-start gap-1.5 text-sm text-slate-500"><Route class="mt-0.5 size-4 shrink-0" />{{ tour.route }}</p><p class="mt-2 text-sm text-slate-500">{{ tour.desc }}</p></div><ChevronRight class="mt-1 hidden size-5 shrink-0 text-slate-300 sm:block" /></div><div class="mt-5 flex flex-wrap items-end justify-between gap-4"><div><p class="text-xs text-slate-400">Từ</p><p class="text-lg font-black">{{ tour.price }}<span class="text-xs font-medium text-slate-400">/nhóm</span></p></div><span class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-bold text-white">Chọn tour</span></div></div></div></button>
                </section>
                <aside class="h-fit rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:sticky lg:top-6"><p class="text-sm font-bold text-indigo-600">ĐẶT TOUR</p><h2 class="mt-1 break-words text-xl font-black">{{ selectedTour.name }}</h2><div class="mt-5 space-y-3"><label class="block"><span class="mb-1.5 block text-xs font-bold uppercase text-slate-400">Ngày khởi hành</span><div class="flex items-center gap-2 rounded-2xl bg-slate-50 px-4 py-3"><CalendarDays class="size-5 shrink-0 text-indigo-600" /><input v-model="tourDate" :min="minDate" type="date" class="w-full min-w-0 border-0 bg-transparent p-0 text-sm outline-none focus:ring-0" /></div></label><label class="block"><span class="mb-1.5 block text-xs font-bold uppercase text-slate-400">Số khách</span><div class="flex items-center gap-2 rounded-2xl bg-slate-50 px-4 py-3"><Users class="size-5 shrink-0 text-indigo-600" /><select v-model="guests" class="w-full min-w-0 border-0 bg-transparent p-0 text-sm font-semibold outline-none focus:ring-0"><option v-for="n in 16" :key="n" :value="n">{{ n }} người</option></select></div></label><label class="block"><span class="mb-1.5 block text-xs font-bold uppercase text-slate-400">Điểm đón</span><div class="flex items-center gap-2 rounded-2xl bg-slate-50 px-4 py-3"><MapPin class="size-5 shrink-0 text-indigo-600" /><input v-model="pickup" class="w-full min-w-0 border-0 bg-transparent p-0 text-sm outline-none focus:ring-0" placeholder="Khách sạn / địa chỉ của bạn" /></div></label></div><div class="mt-6 border-t border-slate-100 pt-5"><div class="flex items-center justify-between text-sm"><span class="text-slate-500">Khách</span><span class="font-bold">{{ guests }} người</span></div><div class="mt-2 flex items-center justify-between gap-3"><span class="font-semibold">Tạm tính</span><span class="text-right text-xl font-black">{{ selectedTour.price }}</span></div><Link :href="bookingUrl" class="mt-4 flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3.5 text-sm font-bold text-white hover:bg-indigo-500">Tiếp tục đặt xe</Link><p class="mt-3 text-center text-xs text-slate-400">Thông tin tour sẽ được chuyển sang biểu mẫu đặt xe để nhân viên xác nhận.</p></div></aside>
            </div>
        </main>
    </div>
</template>
