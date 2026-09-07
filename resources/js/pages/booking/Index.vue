<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ArrowRight, CalendarDays, CarFront, CheckCircle2, Clock3, Mail, MapPin, Phone, Users } from '@lucide/vue';

interface Vehicle {
    id: number;
    name: string;
    brand: string;
    model: string;
    type: string | null;
    seats: number;
    description: string | null;
    image: string | null;
    price: string | number;
}

const props = defineProps<{ vehicles: Vehicle[] }>();
const page = usePage();

const form = useForm({
    customer_name: '', phone: '', email: '', pickup_location: '', destination: '', travel_date: '',
    pickup_time: '09:00', passengers: 1, vehicle_id: props.vehicles[0]?.id ?? null, notes: '',
});

const minDate = new Date().toISOString().slice(0, 10);
const selectedVehicle = computed(() => props.vehicles.find((vehicle) => vehicle.id === form.vehicle_id));
const success = computed(() => page.props.flash?.success as string | undefined);

const submit = () => form.post('/dat-xe', { preserveScroll: true, onSuccess: () => form.reset('customer_name', 'phone', 'email', 'pickup_location', 'destination', 'travel_date', 'notes') });
</script>

<template>
    <Head title="Đặt xe" />
    <div class="min-h-screen bg-slate-50 text-slate-950">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-2 font-black tracking-tight"><span class="grid size-9 place-items-center rounded-xl bg-slate-950 text-white"><CarFront class="size-5" /></span><span>Car<span class="text-indigo-600">Booking</span></span></Link>
                <nav class="hidden items-center gap-6 text-sm font-semibold md:flex"><Link href="/dat-xe" class="text-indigo-600">Đặt xe</Link><Link href="/thue-xe" class="hover:text-indigo-600">Thuê xe</Link><a href="/tour" class="hover:text-indigo-600">Theo tour</a><Link href="/blog" class="hover:text-indigo-600">Blog</Link></nav>
                <Link href="/" class="text-sm font-semibold text-slate-500 hover:text-slate-900">Trang chủ</Link>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
            <div class="mb-8"><p class="text-sm font-bold uppercase tracking-widest text-indigo-600">Đặt xe</p><h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Đặt chuyến xe theo nhu cầu</h1><p class="mt-2 max-w-2xl text-slate-500">Không cần tài khoản. Điền thông tin, chúng tôi tiếp nhận yêu cầu và liên hệ xác nhận.</p></div>

            <div v-if="success" class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800"><CheckCircle2 class="mt-0.5 size-5 shrink-0" /><div><p class="font-bold">Đặt xe thành công</p><p class="mt-1 text-sm">{{ success }}</p></div></div>

            <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-[1.15fr_.85fr]">
                <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-start justify-between"><div><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Thông tin chuyến đi</p><h2 class="mt-1 text-xl font-black">Nhập thông tin đặt xe</h2></div><span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">Không cần đăng nhập</span></div>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <label><span class="mb-1.5 block text-sm font-semibold">Họ và tên <b class="text-rose-500">*</b></span><input v-model="form.customer_name" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" placeholder="Nguyễn Văn A" /><p v-if="form.errors.customer_name" class="mt-1 text-xs text-rose-600">{{ form.errors.customer_name }}</p></label>
                        <label><span class="mb-1.5 block text-sm font-semibold">Số điện thoại <b class="text-rose-500">*</b></span><div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100"><Phone class="size-4 text-slate-400" /><input v-model="form.phone" type="tel" class="w-full border-0 p-0 outline-none focus:ring-0" placeholder="0901234567" /></div><p v-if="form.errors.phone" class="mt-1 text-xs text-rose-600">{{ form.errors.phone }}</p></label>
                        <label class="sm:col-span-2"><span class="mb-1.5 block text-sm font-semibold">Email <b class="text-rose-500">*</b></span><div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100"><Mail class="size-4 text-slate-400" /><input v-model="form.email" type="email" class="w-full border-0 p-0 outline-none focus:ring-0" placeholder="you@example.com" /></div><p v-if="form.errors.email" class="mt-1 text-xs text-rose-600">{{ form.errors.email }}</p></label>
                        <label class="sm:col-span-2"><span class="mb-1.5 block text-sm font-semibold">Điểm đón <b class="text-rose-500">*</b></span><div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3"><MapPin class="size-5 text-indigo-600" /><input v-model="form.pickup_location" class="w-full border-0 p-0 outline-none focus:ring-0" placeholder="Nhập địa chỉ hoặc địa điểm đón" /></div><p v-if="form.errors.pickup_location" class="mt-1 text-xs text-rose-600">{{ form.errors.pickup_location }}</p></label>
                        <label class="sm:col-span-2"><span class="mb-1.5 block text-sm font-semibold">Điểm đến <b class="text-rose-500">*</b></span><div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3"><ArrowRight class="size-5 text-indigo-600" /><input v-model="form.destination" class="w-full border-0 p-0 outline-none focus:ring-0" placeholder="Nhập địa chỉ hoặc địa điểm đến" /></div><p v-if="form.errors.destination" class="mt-1 text-xs text-rose-600">{{ form.errors.destination }}</p></label>
                        <label><span class="mb-1.5 block text-sm font-semibold">Ngày đi <b class="text-rose-500">*</b></span><div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3"><CalendarDays class="size-5 text-indigo-600" /><input v-model="form.travel_date" :min="minDate" type="date" class="w-full border-0 p-0 outline-none focus:ring-0" /></div><p v-if="form.errors.travel_date" class="mt-1 text-xs text-rose-600">{{ form.errors.travel_date }}</p></label>
                        <label><span class="mb-1.5 block text-sm font-semibold">Giờ đón <b class="text-rose-500">*</b></span><div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3"><Clock3 class="size-5 text-indigo-600" /><input v-model="form.pickup_time" type="time" class="w-full border-0 p-0 outline-none focus:ring-0" /></div><p v-if="form.errors.pickup_time" class="mt-1 text-xs text-rose-600">{{ form.errors.pickup_time }}</p></label>
                        <label><span class="mb-1.5 block text-sm font-semibold">Hành khách <b class="text-rose-500">*</b></span><div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3"><Users class="size-5 text-indigo-600" /><select v-model="form.passengers" class="w-full border-0 bg-transparent p-0 outline-none focus:ring-0"><option v-for="n in 12" :key="n" :value="n">{{ n }} người</option></select></div></label>
                        <label><span class="mb-1.5 block text-sm font-semibold">Loại xe</span><div class="flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3"><CarFront class="size-5 text-indigo-600" /><select v-model="form.vehicle_id" class="w-full border-0 bg-transparent p-0 outline-none focus:ring-0"><option :value="null">Để chúng tôi tư vấn</option><option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">{{ vehicle.name }} · {{ vehicle.seats }} chỗ</option></select></div></label>
                        <label class="sm:col-span-2"><span class="mb-1.5 block text-sm font-semibold">Ghi chú thêm</span><textarea v-model="form.notes" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" placeholder="Ví dụ: có trẻ nhỏ, nhiều hành lý, điểm đón cụ thể..." /></label>
                    </div>
                </section>

                <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
                    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Xe đã chọn</p><div v-if="selectedVehicle" class="mt-4 overflow-hidden rounded-2xl border border-slate-200"><div class="flex aspect-[16/9] items-center justify-center bg-slate-100"><img v-if="selectedVehicle.image" :src="selectedVehicle.image" :alt="selectedVehicle.name" class="h-full w-full object-cover" /><CarFront v-else class="size-14 text-slate-400" /></div><div class="p-4"><h3 class="text-lg font-black">{{ selectedVehicle.name }}</h3><p class="mt-1 text-sm text-slate-500">{{ selectedVehicle.brand }} · {{ selectedVehicle.model }} · {{ selectedVehicle.seats }} chỗ</p><p v-if="selectedVehicle.description" class="mt-2 text-sm text-slate-500">{{ selectedVehicle.description }}</p></div></div><div v-else class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">Bạn có thể để hệ thống tư vấn xe phù hợp.</div></section>
                    <section class="rounded-3xl bg-slate-950 p-5 text-white shadow-xl sm:p-6"><p class="text-sm font-semibold text-slate-300">Sau khi gửi</p><h3 class="mt-2 text-xl font-black">Chúng tôi sẽ liên hệ xác nhận</h3><p class="mt-2 text-sm leading-6 text-slate-400">Yêu cầu được lưu với trạng thái chờ xác nhận. Không yêu cầu thanh toán online.</p><button :disabled="form.processing" class="mt-5 w-full rounded-2xl bg-indigo-500 px-5 py-3.5 font-bold transition hover:bg-indigo-400 disabled:cursor-not-allowed disabled:opacity-60">{{ form.processing ? 'Đang gửi...' : 'Gửi yêu cầu đặt xe' }}</button></section>
                </aside>
            </form>
        </main>
    </div>
</template>
