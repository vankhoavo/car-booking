<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Car, Pencil, Plus, Trash2, X } from 'lucide-vue-next';
import { ref } from 'vue';

type Vehicle = {
    id: number;
    name: string;
    brand: string;
    model: string;
    type: string | null;
    seats: number;
    description: string | null;
    image: string | null;
    price: string | number;
    status: 'available' | 'maintenance' | 'inactive';
};

const props = defineProps<{ vehicles: Vehicle[] }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Quản trị', href: '/admin' },
            { title: 'Quản lý xe', href: '/admin/vehicles' },
        ],
    },
});

const showForm = ref(false);
const editing = ref<Vehicle | null>(null);

const form = useForm({
    name: '', brand: '', model: '', type: '', seats: 4,
    description: '', image: '', price: 0, status: 'available',
});

const openCreate = () => {
    editing.value = null;
    form.reset();
    form.seats = 4;
    form.status = 'available';
    showForm.value = true;
};

const openEdit = (vehicle: Vehicle) => {
    editing.value = vehicle;
    form.name = vehicle.name;
    form.brand = vehicle.brand;
    form.model = vehicle.model;
    form.type = vehicle.type ?? '';
    form.seats = vehicle.seats;
    form.description = vehicle.description ?? '';
    form.image = vehicle.image ?? '';
    form.price = Number(vehicle.price);
    form.status = vehicle.status;
    showForm.value = true;
};

const submit = () => {
    if (editing.value) {
        form.put(`/admin/vehicles/${editing.value.id}`, { onSuccess: () => { showForm.value = false; } });
        return;
    }

    form.post('/admin/vehicles', { onSuccess: () => { showForm.value = false; } });
};

const remove = (vehicle: Vehicle) => {
    if (window.confirm(`Xóa xe “${vehicle.name}”?`)) {
        router.delete(`/admin/vehicles/${vehicle.id}`);
    }
};

const statusLabel = (status: Vehicle['status']) => ({
    available: 'Sẵn sàng',
    maintenance: 'Bảo trì',
    inactive: 'Ngừng sử dụng',
}[status]);

const statusClass = (status: Vehicle['status']) => ({
    available: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    maintenance: 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
    inactive: 'bg-muted text-muted-foreground',
}[status]);
</script>

<template>
    <Head title="Quản lý xe" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Quản lý xe</h1>
                <p class="mt-1 text-sm text-muted-foreground">Thêm, sửa, cập nhật trạng thái và xóa xe.</p>
            </div>
            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground" @click="openCreate">
                <Plus class="size-4" /> Thêm xe
            </button>
        </div>

        <section class="overflow-hidden rounded-xl border bg-card shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="bg-muted/40 text-muted-foreground">
                        <tr>
                            <th class="px-5 py-3 font-medium">Xe</th>
                            <th class="px-5 py-3 font-medium">Loại</th>
                            <th class="px-5 py-3 font-medium">Chỗ</th>
                            <th class="px-5 py-3 font-medium">Giá</th>
                            <th class="px-5 py-3 font-medium">Trạng thái</th>
                            <th class="px-5 py-3 text-right font-medium">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="vehicle in props.vehicles" :key="vehicle.id" class="border-t">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-muted">
                                        <img v-if="vehicle.image" :src="vehicle.image" :alt="vehicle.name" class="size-full object-cover" />
                                        <Car v-else class="size-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ vehicle.name }}</div>
                                        <div class="text-muted-foreground">{{ vehicle.brand }} {{ vehicle.model }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">{{ vehicle.type || '—' }}</td>
                            <td class="px-5 py-4">{{ vehicle.seats }}</td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ Number(vehicle.price).toLocaleString('vi-VN') }} đ</td>
                            <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="statusClass(vehicle.status)">{{ statusLabel(vehicle.status) }}</span></td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" class="rounded-md border p-2 hover:bg-muted" title="Sửa" @click="openEdit(vehicle)"><Pencil class="size-4" /></button>
                                    <button type="button" class="rounded-md border p-2 text-destructive hover:bg-muted" title="Xóa" @click="remove(vehicle)"><Trash2 class="size-4" /></button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="props.vehicles.length === 0"><td colspan="6" class="px-5 py-10 text-center text-muted-foreground">Chưa có xe nào.</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <div v-if="showForm" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/40 p-4 md:items-center">
            <div class="w-full max-w-2xl rounded-xl border bg-card p-5 shadow-xl md:p-6">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">{{ editing ? 'Cập nhật xe' : 'Thêm xe mới' }}</h2>
                        <p class="mt-1 text-sm text-muted-foreground">Thông tin hiển thị trên trang Thuê xe.</p>
                    </div>
                    <button type="button" class="rounded-md p-2 hover:bg-muted" @click="showForm = false"><X class="size-5" /></button>
                </div>

                <form class="grid gap-4 sm:grid-cols-2" @submit.prevent="submit">
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Tên xe *</span><input v-model="form.name" class="rounded-lg border bg-background px-3 py-2" required /><small v-if="form.errors.name" class="text-destructive">{{ form.errors.name }}</small></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Hãng xe *</span><input v-model="form.brand" class="rounded-lg border bg-background px-3 py-2" required /></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Dòng xe *</span><input v-model="form.model" class="rounded-lg border bg-background px-3 py-2" required /></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Loại xe</span><input v-model="form.type" class="rounded-lg border bg-background px-3 py-2" placeholder="Sedan, SUV..." /></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Số chỗ *</span><input v-model.number="form.seats" type="number" min="1" max="60" class="rounded-lg border bg-background px-3 py-2" required /></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Giá tham khảo *</span><input v-model.number="form.price" type="number" min="0" step="1000" class="rounded-lg border bg-background px-3 py-2" required /></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Trạng thái *</span><select v-model="form.status" class="rounded-lg border bg-background px-3 py-2"><option value="available">Sẵn sàng</option><option value="maintenance">Bảo trì</option><option value="inactive">Ngừng sử dụng</option></select></label>
                    <label class="grid gap-1.5 sm:col-span-2"><span class="text-sm font-medium">Đường dẫn hình ảnh</span><input v-model="form.image" type="url" class="rounded-lg border bg-background px-3 py-2" placeholder="https://..." /></label>
                    <label class="grid gap-1.5 sm:col-span-2"><span class="text-sm font-medium">Mô tả</span><textarea v-model="form.description" rows="3" class="rounded-lg border bg-background px-3 py-2" /></label>
                    <div class="flex justify-end gap-2 sm:col-span-2">
                        <button type="button" class="rounded-lg border px-4 py-2 text-sm" @click="showForm = false">Hủy</button>
                        <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground" :disabled="form.processing">{{ form.processing ? 'Đang lưu...' : 'Lưu xe' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
