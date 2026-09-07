<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Eye, ClipboardList } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Booking = {
    id: number; customer_name: string; phone: string; email: string; pickup_location: string;
    destination: string; travel_date: string; pickup_time: string; passengers: number;
    notes: string | null; status: 'pending' | 'confirmed' | 'cancelled' | 'completed';
    vehicle: { id: number; name: string; brand: string; model: string; seats: number } | null;
};

const props = defineProps<{ bookings: Booking[]; filters: { status: string } }>();
const selected = ref<Booking | null>(null);
const status = ref(props.filters.status || '');

const filtered = computed(() => props.bookings);
const filter = () => router.get('/admin/bookings', status.value ? { status: status.value } : {}, { preserveState: true, replace: true });
const updateStatus = (booking: Booking, next: Booking['status']) => {
    router.put(`/admin/bookings/${booking.id}`, { status: next }, { preserveScroll: true });
};
const statusLabel = (value: Booking['status']) => ({ pending: 'Chờ xác nhận', confirmed: 'Đã xác nhận', cancelled: 'Đã hủy', completed: 'Hoàn thành' }[value]);
const statusClass = (value: Booking['status']) => ({
    pending: 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
    confirmed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    cancelled: 'bg-muted text-muted-foreground',
    completed: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
}[value]);
const date = (value: string) => new Date(value).toLocaleDateString('vi-VN');
</script>

<template>
    <Head title="Đơn đặt xe" />
    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div><h1 class="text-2xl font-semibold tracking-tight">Đơn đặt xe</h1><p class="mt-1 text-sm text-muted-foreground">Theo dõi và xử lý các yêu cầu đặt xe.</p></div>
            <div class="flex items-center gap-2">
                <select v-model="status" class="rounded-lg border bg-background px-3 py-2 text-sm" @change="filter">
                    <option value="">Tất cả trạng thái</option><option value="pending">Chờ xác nhận</option><option value="confirmed">Đã xác nhận</option><option value="cancelled">Đã hủy</option><option value="completed">Hoàn thành</option>
                </select>
            </div>
        </div>
        <section class="overflow-hidden rounded-xl border bg-card shadow-sm">
            <div class="overflow-x-auto"><table class="w-full min-w-[1050px] text-left text-sm">
                <thead class="bg-muted/40 text-muted-foreground"><tr><th class="px-5 py-3 font-medium">Khách hàng</th><th class="px-5 py-3 font-medium">Xe</th><th class="px-5 py-3 font-medium">Ngày / giờ</th><th class="px-5 py-3 font-medium">Hành trình</th><th class="px-5 py-3 font-medium">Khách</th><th class="px-5 py-3 font-medium">Trạng thái</th><th class="px-5 py-3 text-right font-medium">Thao tác</th></tr></thead>
                <tbody>
                    <tr v-for="booking in filtered" :key="booking.id" class="border-t">
                        <td class="px-5 py-4"><div class="font-medium">{{ booking.customer_name }}</div><div class="text-muted-foreground">{{ booking.phone }}</div></td>
                        <td class="px-5 py-4">{{ booking.vehicle?.name || 'Chưa chọn xe' }}</td>
                        <td class="px-5 py-4 whitespace-nowrap">{{ date(booking.travel_date) }}<div class="text-muted-foreground">{{ booking.pickup_time }}</div></td>
                        <td class="max-w-[280px] px-5 py-4"><div>{{ booking.pickup_location }}</div><div class="text-muted-foreground">→ {{ booking.destination }}</div></td>
                        <td class="px-5 py-4">{{ booking.passengers }}</td>
                        <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="statusClass(booking.status)">{{ statusLabel(booking.status) }}</span></td>
                        <td class="px-5 py-4"><div class="flex justify-end gap-2"><button class="rounded-md border p-2 hover:bg-muted" title="Xem chi tiết" @click="selected = booking"><Eye class="size-4" /></button><select class="rounded-md border bg-background px-2 py-1.5 text-xs" :value="booking.status" @change="updateStatus(booking, ($event.target as HTMLSelectElement).value as Booking['status'])"><option value="pending">Chờ xác nhận</option><option value="confirmed">Đã xác nhận</option><option value="cancelled">Đã hủy</option><option value="completed">Hoàn thành</option></select></div></td>
                    </tr>
                    <tr v-if="filtered.length === 0"><td colspan="7" class="px-5 py-10 text-center text-muted-foreground"><ClipboardList class="mx-auto mb-2 size-7" />Chưa có đơn đặt xe.</td></tr>
                </tbody>
            </table></div>
        </section>
        <div v-if="selected" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @click.self="selected = null"><div class="w-full max-w-xl rounded-xl border bg-card p-6 shadow-xl"><div class="flex items-start justify-between"><div><h2 class="text-lg font-semibold">Chi tiết đơn #{{ selected.id }}</h2><p class="text-sm text-muted-foreground">{{ selected.customer_name }}</p></div><button class="rounded-md px-2 py-1 text-xl hover:bg-muted" @click="selected = null">×</button></div><div class="mt-5 grid gap-3 text-sm sm:grid-cols-2"><div><b>Điện thoại:</b> {{ selected.phone }}</div><div><b>Email:</b> {{ selected.email }}</div><div><b>Xe:</b> {{ selected.vehicle?.name || 'Chưa chọn xe' }}</div><div><b>Số khách:</b> {{ selected.passengers }}</div><div><b>Ngày:</b> {{ date(selected.travel_date) }}</div><div><b>Giờ đón:</b> {{ selected.pickup_time }}</div><div class="sm:col-span-2"><b>Điểm đón:</b> {{ selected.pickup_location }}</div><div class="sm:col-span-2"><b>Điểm đến:</b> {{ selected.destination }}</div><div v-if="selected.notes" class="sm:col-span-2"><b>Ghi chú:</b> {{ selected.notes }}</div></div></div></div>
    </div>
</template>
