<script setup lang="ts">
import { computed, ref } from 'vue'
import {
    ArrowRight,
    CalendarDays,
    CarFront,
    ChevronDown,
    Clock3,
    MapPin,
    Menu,
    Navigation,
    Phone,
    Search,
    ShieldCheck,
    Sparkles,
    Star,
    Users,
    X,
} from '@lucide/vue'

type BookingType = 'tour' | 'daily' | 'self-drive'

const bookingType = ref<BookingType>('tour')
const mobileOpen = ref(false)
const pickup = ref('Đà Nẵng')
const destination = ref('Hội An')
const date = ref('')
const guests = ref('2 người')

const tabs: Array<{ id: BookingType; label: string; caption: string }> = [
    { id: 'tour', label: 'Đặt theo tour', caption: 'Lịch trình trọn gói' },
    { id: 'daily', label: 'Thuê theo ngày', caption: 'Linh hoạt theo nhu cầu' },
    { id: 'self-drive', label: 'Thuê tự lái', caption: 'Tự do khám phá' },
]

const bookingTitle = computed(() => {
    if (bookingType.value === 'daily') return 'Thuê xe theo ngày, chủ động từng hành trình.'
    if (bookingType.value === 'self-drive') return 'Tự lái, tự do. Chọn chiếc xe hợp với bạn.'
    return 'Đi xa hơn. Đặt xe nhanh hơn. Trải nghiệm tốt hơn.'
})

const bookingHint = computed(() => {
    if (bookingType.value === 'daily') return 'Chọn ngày nhận xe và số ngày bạn cần sử dụng.'
    if (bookingType.value === 'self-drive') return 'Xe đời mới, thủ tục rõ ràng, nhận xe thuận tiện.'
    return 'Tìm lịch trình phù hợp và nhận báo giá ngay trong vài bước.'
})

const popular = [
    {
        title: 'Đà Nẵng → Hội An',
        meta: '35 km · từ 450.000đ',
        image: 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?auto=format&fit=crop&w=900&q=85',
    },
    {
        title: 'Đà Nẵng → Huế',
        meta: '100 km · từ 1.050.000đ',
        image: 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=900&q=85',
    },
    {
        title: 'Đà Nẵng → Bà Nà Hills',
        meta: '30 km · từ 650.000đ',
        image: 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=85',
    },
]

const fleet = [
    { name: 'Toyota Camry', type: 'Sedan · 5 chỗ', price: '1.200.000đ', image: 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?auto=format&fit=crop&w=1000&q=85' },
    { name: 'Kia Carnival', type: 'MPV · 7 chỗ', price: '1.650.000đ', image: 'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?auto=format&fit=crop&w=1000&q=85' },
    { name: 'Ford Transit', type: 'Van · 16 chỗ', price: '1.900.000đ', image: 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1000&q=85' },
]
</script>

<template>
    <div class="min-h-screen overflow-x-hidden bg-[#f7f7f5] text-[#171816] selection:bg-[#d5b36a] selection:text-[#171816]">
        <header class="absolute inset-x-0 top-0 z-50">
            <div class="mx-auto flex h-24 max-w-7xl items-center justify-between px-5 lg:px-8">
                <a href="#" class="flex items-center gap-3" aria-label="Card Booking">
                    <div class="grid size-10 place-items-center rounded-2xl bg-white text-[#171816] shadow-lg shadow-black/10">
                        <CarFront :size="21" :stroke-width="2.4" />
                    </div>
                    <div class="leading-none">
                        <div class="text-[17px] font-black tracking-[-0.04em] text-white">CARD</div>
                        <div class="mt-1 text-[9px] font-semibold uppercase tracking-[0.28em] text-white/55">Booking</div>
                    </div>
                </a>

                <nav class="hidden items-center gap-8 text-sm font-medium text-white/75 lg:flex">
                    <a href="#services" class="transition hover:text-white">Dịch vụ</a>
                    <a href="#fleet" class="transition hover:text-white">Đội xe</a>
                    <a href="#routes" class="transition hover:text-white">Lịch trình</a>
                    <a href="#why-us" class="transition hover:text-white">Vì sao chọn chúng tôi</a>
                </nav>

                <div class="hidden items-center gap-4 lg:flex">
                    <a href="tel:+84900000000" class="flex items-center gap-2 text-sm font-semibold text-white/80 transition hover:text-white">
                        <Phone :size="15" /> 0900 000 000
                    </a>
                    <a href="#booking" class="rounded-full bg-white px-5 py-3 text-sm font-bold text-[#171816] shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:bg-[#f2dfb1]">Đặt xe ngay</a>
                </div>

                <button class="grid size-11 place-items-center rounded-full border border-white/15 bg-white/10 text-white backdrop-blur lg:hidden" @click="mobileOpen = !mobileOpen" aria-label="Mở menu">
                    <X v-if="mobileOpen" :size="21" />
                    <Menu v-else :size="21" />
                </button>
            </div>

            <div v-if="mobileOpen" class="mx-4 rounded-3xl border border-white/10 bg-[#171816]/95 p-5 text-white shadow-2xl backdrop-blur-xl lg:hidden">
                <nav class="grid gap-1 text-sm">
                    <a v-for="item in ['Dịch vụ', 'Đội xe', 'Lịch trình', 'Vì sao chọn chúng tôi']" :key="item" href="#" class="rounded-xl px-4 py-3 text-white/80 hover:bg-white/10 hover:text-white" @click="mobileOpen = false">{{ item }}</a>
                </nav>
                <a href="#booking" class="mt-3 block rounded-xl bg-white px-4 py-3 text-center text-sm font-bold text-[#171816]">Đặt xe ngay</a>
            </div>
        </header>

        <main>
            <section class="relative min-h-[780px] overflow-hidden bg-[#101310]">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_20%,rgba(213,179,106,.22),transparent_30%),linear-gradient(90deg,rgba(10,12,10,.94)_0%,rgba(10,12,10,.65)_48%,rgba(10,12,10,.18)_100%)]"></div>
                <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=2200&q=90" alt="Xe sang trên cung đường ven biển" class="absolute inset-0 size-full object-cover object-center opacity-80" />
                <div class="absolute inset-0 bg-gradient-to-t from-[#101310] via-transparent to-[#101310]/20"></div>

                <div class="relative mx-auto flex min-h-[780px] max-w-7xl flex-col justify-center px-5 pb-28 pt-36 lg:px-8">
                    <div class="max-w-3xl">
                        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-2 text-xs font-semibold text-white/85 backdrop-blur-md">
                            <Sparkles :size="14" class="text-[#e7c87d]" />
                            Trải nghiệm di chuyển cao cấp
                        </div>
                        <h1 class="max-w-3xl text-5xl font-black leading-[.94] tracking-[-0.065em] text-white sm:text-6xl lg:text-[84px]">
                            Hành trình đẹp bắt đầu từ <span class="text-[#e4c476]">một chiếc xe tốt.</span>
                        </h1>
                        <p class="mt-7 max-w-xl text-base leading-7 text-white/65 sm:text-lg">
                            Đặt xe đi tour, thuê theo ngày hoặc tự lái. Chọn xe, chọn lịch trình và để chúng tôi lo phần còn lại.
                        </p>
                        <div class="mt-9 flex flex-wrap items-center gap-3 text-xs font-medium text-white/60">
                            <span class="flex items-center gap-2"><ShieldCheck :size="15" class="text-[#e4c476]" /> Đối tác tin cậy</span>
                            <span class="size-1 rounded-full bg-white/25"></span>
                            <span class="flex items-center gap-2"><Star :size="14" class="fill-[#e4c476] text-[#e4c476]" /> 4.9/5 đánh giá</span>
                            <span class="size-1 rounded-full bg-white/25"></span>
                            <span class="flex items-center gap-2"><Users :size="15" /> 10.000+ khách hàng</span>
                        </div>
                    </div>
                </div>

                <div class="absolute inset-x-0 bottom-0 z-10 translate-y-1/2 px-5 lg:px-8">
                    <div id="booking" class="mx-auto max-w-7xl rounded-[28px] border border-black/5 bg-white p-2 shadow-[0_28px_80px_rgba(0,0,0,.22)]">
                        <div class="flex gap-1 overflow-x-auto px-1 pt-1">
                            <button v-for="tab in tabs" :key="tab.id" class="min-w-[155px] flex-1 rounded-2xl px-4 py-3 text-left transition" :class="bookingType === tab.id ? 'bg-[#171816] text-white shadow-lg' : 'text-[#777a74] hover:bg-[#f4f4f1]'" @click="bookingType = tab.id">
                                <div class="text-sm font-bold">{{ tab.label }}</div>
                                <div class="mt-0.5 text-[11px] opacity-60">{{ tab.caption }}</div>
                            </button>
                        </div>
                        <div class="grid gap-2 p-1 sm:grid-cols-2 lg:grid-cols-[1.1fr_1.1fr_1fr_.9fr_auto]">
                            <label class="group flex items-center gap-3 rounded-2xl bg-[#f5f5f2] px-4 py-3.5">
                                <MapPin :size="18" class="text-[#a08a5d]" />
                                <span class="min-w-0 flex-1"><span class="block text-[10px] font-bold uppercase tracking-wider text-[#8d908a]">Điểm đón</span><input v-model="pickup" class="mt-0.5 w-full bg-transparent text-sm font-bold outline-none" /></span>
                            </label>
                            <label class="group flex items-center gap-3 rounded-2xl bg-[#f5f5f2] px-4 py-3.5">
                                <Navigation :size="18" class="text-[#a08a5d]" />
                                <span class="min-w-0 flex-1"><span class="block text-[10px] font-bold uppercase tracking-wider text-[#8d908a]">Điểm đến</span><input v-model="destination" class="mt-0.5 w-full bg-transparent text-sm font-bold outline-none" /></span>
                            </label>
                            <label class="flex items-center gap-3 rounded-2xl bg-[#f5f5f2] px-4 py-3.5">
                                <CalendarDays :size="18" class="text-[#a08a5d]" />
                                <span class="min-w-0 flex-1"><span class="block text-[10px] font-bold uppercase tracking-wider text-[#8d908a]">Ngày đi</span><input v-model="date" type="date" class="mt-0.5 w-full bg-transparent text-sm font-bold outline-none" /></span>
                            </label>
                            <label class="flex items-center gap-3 rounded-2xl bg-[#f5f5f2] px-4 py-3.5">
                                <Users :size="18" class="text-[#a08a5d]" />
                                <span class="min-w-0 flex-1"><span class="block text-[10px] font-bold uppercase tracking-wider text-[#8d908a]">Hành khách</span><select v-model="guests" class="mt-0.5 w-full bg-transparent text-sm font-bold outline-none"><option>1 người</option><option>2 người</option><option>4 người</option><option>7 người</option><option>16 người</option></select></span>
                            </label>
                            <button class="flex min-h-[58px] items-center justify-center gap-2 rounded-2xl bg-[#171816] px-6 text-sm font-bold text-white transition hover:bg-[#2a2d29] active:scale-[.98]">
                                <Search :size="18" /> Tìm xe
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section id="services" class="mx-auto max-w-7xl px-5 pb-24 pt-44 lg:px-8 lg:pt-36">
                <div class="grid gap-12 lg:grid-cols-[.8fr_1.2fr] lg:items-end">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.24em] text-[#a08a5d]">Dịch vụ của chúng tôi</p>
                        <h2 class="mt-4 text-4xl font-black tracking-[-.05em] sm:text-5xl">Một nền tảng.<br />Mọi hành trình.</h2>
                    </div>
                    <div class="flex items-end justify-between gap-8">
                        <p class="max-w-xl text-sm leading-6 text-[#70736d]">Thiết kế để bạn đặt xe dễ dàng trên điện thoại, minh bạch về giá và không cần tạo tài khoản để bắt đầu.</p>
                        <a href="#fleet" class="hidden shrink-0 items-center gap-2 text-sm font-bold lg:flex">Xem đội xe <ArrowRight :size="16" /></a>
                    </div>
                </div>

                <div class="mt-12 grid gap-4 md:grid-cols-3">
                    <article class="group relative min-h-[330px] overflow-hidden rounded-[30px] bg-[#171816] p-7 text-white">
                        <div class="relative z-10 flex size-12 items-center justify-center rounded-2xl bg-white/10"><Navigation :size="22" /></div>
                        <div class="absolute -right-10 -top-10 size-52 rounded-full bg-[#e2c176]/10 blur-2xl transition duration-500 group-hover:bg-[#e2c176]/20"></div>
                        <div class="absolute inset-x-0 bottom-0 z-10 p-7"><div class="mb-2 text-xs font-bold uppercase tracking-[.18em] text-[#e2c176]">01 · Tour</div><h3 class="text-2xl font-black tracking-[-.04em]">Đi tour trọn gói</h3><p class="mt-2 max-w-xs text-sm leading-6 text-white/50">Lịch trình được chuẩn bị sẵn, tài xế chuyên nghiệp và giá rõ ràng.</p></div>
                    </article>
                    <article class="group relative min-h-[330px] overflow-hidden rounded-[30px] bg-[#e7e0d0] p-7 text-[#171816]">
                        <div class="relative z-10 flex size-12 items-center justify-center rounded-2xl bg-black/5"><Clock3 :size="22" /></div>
                        <div class="absolute -bottom-20 -right-8 size-64 rounded-full bg-white blur-3xl transition duration-500 group-hover:scale-110"></div>
                        <div class="absolute inset-x-0 bottom-0 z-10 p-7"><div class="mb-2 text-xs font-bold uppercase tracking-[.18em] text-[#967c48]">02 · Daily</div><h3 class="text-2xl font-black tracking-[-.04em]">Thuê xe theo ngày</h3><p class="mt-2 max-w-xs text-sm leading-6 text-black/50">Tự chọn thời gian và loại xe. Phù hợp công tác, gia đình và chuyến đi dài.</p></div>
                    </article>
                    <article class="group relative min-h-[330px] overflow-hidden rounded-[30px] bg-[#dfe5df] p-7 text-[#171816]">
                        <div class="relative z-10 flex size-12 items-center justify-center rounded-2xl bg-black/5"><CarFront :size="22" /></div>
                        <div class="absolute -right-16 top-12 size-60 rounded-full bg-white/70 blur-3xl transition duration-500 group-hover:scale-110"></div>
                        <div class="absolute inset-x-0 bottom-0 z-10 p-7"><div class="mb-2 text-xs font-bold uppercase tracking-[.18em] text-[#58705b]">03 · Self-drive</div><h3 class="text-2xl font-black tracking-[-.04em]">Thuê xe tự lái</h3><p class="mt-2 max-w-xs text-sm leading-6 text-black/50">Nhận xe nhanh, thủ tục gọn và trải nghiệm riêng tư cho hành trình của bạn.</p></div>
                    </article>
                </div>
            </section>

            <section id="routes" class="border-y border-black/5 bg-white py-24">
                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                        <div><p class="text-xs font-black uppercase tracking-[.24em] text-[#a08a5d]">Lộ trình được yêu thích</p><h2 class="mt-3 text-4xl font-black tracking-[-.05em] sm:text-5xl">Đi đâu hôm nay?</h2></div>
                        <button class="flex items-center gap-2 text-sm font-bold text-[#70736d]">Xem tất cả <ArrowRight :size="16" /></button>
                    </div>
                    <div class="mt-10 grid gap-5 md:grid-cols-3">
                        <article v-for="item in popular" :key="item.title" class="group overflow-hidden rounded-[26px] border border-black/5 bg-[#f7f7f5]">
                            <div class="relative aspect-[1.45] overflow-hidden"><img :src="item.image" :alt="item.title" class="size-full object-cover transition duration-700 group-hover:scale-105" /><div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div><div class="absolute bottom-4 left-4 rounded-full bg-white/90 px-3 py-1.5 text-[10px] font-bold text-[#171816]">Phổ biến</div></div>
                            <div class="p-5"><h3 class="text-lg font-black tracking-[-.03em]">{{ item.title }}</h3><div class="mt-2 flex items-center justify-between text-xs text-[#858880]"><span>{{ item.meta }}</span><ArrowRight :size="15" class="transition group-hover:translate-x-1" /></div></div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="fleet" class="mx-auto max-w-7xl px-5 py-24 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-[.72fr_1.28fr] lg:items-end">
                    <div><p class="text-xs font-black uppercase tracking-[.24em] text-[#a08a5d]">Đội xe</p><h2 class="mt-4 text-4xl font-black tracking-[-.05em] sm:text-5xl">Chọn chiếc xe<br />đúng với chuyến đi.</h2></div>
                    <div class="flex items-end justify-between gap-6"><p class="max-w-lg text-sm leading-6 text-[#70736d]">Từ sedan tinh tế đến MPV rộng rãi và xe phục vụ đoàn. Hình ảnh, thông tin và mức giá được trình bày rõ ràng trước khi đặt.</p><a href="#" class="hidden items-center gap-2 text-sm font-bold lg:flex">Xem toàn bộ đội xe <ArrowRight :size="16" /></a></div>
                </div>
                <div class="mt-12 grid gap-5 md:grid-cols-3">
                    <article v-for="car in fleet" :key="car.name" class="group overflow-hidden rounded-[28px] bg-white shadow-[0_10px_40px_rgba(0,0,0,.06)] ring-1 ring-black/5">
                        <div class="aspect-[1.4] overflow-hidden bg-[#f0f0ec]"><img :src="car.image" :alt="car.name" class="size-full object-cover transition duration-700 group-hover:scale-105" /></div>
                        <div class="p-5"><div class="flex items-start justify-between gap-4"><div><h3 class="text-xl font-black tracking-[-.04em]">{{ car.name }}</h3><p class="mt-1 text-xs text-[#858880]">{{ car.type }}</p></div><div class="text-right"><div class="text-sm font-black">{{ car.price }}</div><div class="mt-1 text-[10px] text-[#858880]">/ ngày</div></div></div><button class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl border border-black/8 py-3 text-xs font-bold transition hover:bg-[#171816] hover:text-white">Xem chi tiết <ArrowRight :size="14" /></button></div>
                    </article>
                </div>
            </section>

            <section id="why-us" class="relative overflow-hidden bg-[#171816] py-24 text-white">
                <div class="absolute -left-32 top-0 size-96 rounded-full bg-[#d9b96f]/10 blur-3xl"></div>
                <div class="relative mx-auto max-w-7xl px-5 lg:px-8">
                    <div class="grid gap-14 lg:grid-cols-[1fr_1.15fr] lg:items-center">
                        <div><p class="text-xs font-black uppercase tracking-[.24em] text-[#e2c176]">Tại sao là Card Booking?</p><h2 class="mt-4 max-w-xl text-4xl font-black tracking-[-.055em] sm:text-5xl">Mỗi chi tiết nhỏ đều được thiết kế để bạn yên tâm.</h2><p class="mt-6 max-w-lg text-sm leading-7 text-white/50">Không tài khoản bắt buộc. Không quy trình rườm rà. Chỉ cần chọn hành trình, chúng tôi sẽ đồng hành từ lúc đặt xe đến khi bạn hoàn thành chuyến đi.</p></div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-[24px] border border-white/10 bg-white/5 p-6"><ShieldCheck :size="23" class="text-[#e2c176]" /><h3 class="mt-7 text-base font-bold">Minh bạch</h3><p class="mt-2 text-sm leading-6 text-white/45">Giá và điều kiện được hiển thị rõ trước khi xác nhận.</p></div>
                            <div class="rounded-[24px] border border-white/10 bg-white/5 p-6"><Clock3 :size="23" class="text-[#e2c176]" /><h3 class="mt-7 text-base font-bold">Phản hồi nhanh</h3><p class="mt-2 text-sm leading-6 text-white/45">Đội ngũ hỗ trợ sẵn sàng xác nhận và xử lý booking.</p></div>
                            <div class="rounded-[24px] border border-white/10 bg-white/5 p-6"><Star :size="23" class="fill-[#e2c176] text-[#e2c176]" /><h3 class="mt-7 text-base font-bold">Xe được tuyển chọn</h3><p class="mt-2 text-sm leading-6 text-white/45">Ưu tiên xe sạch, an toàn và được kiểm tra định kỳ.</p></div>
                            <div class="rounded-[24px] border border-white/10 bg-white/5 p-6"><Users :size="23" class="text-[#e2c176]" /><h3 class="mt-7 text-base font-bold">Hỗ trợ con người</h3><p class="mt-2 text-sm leading-6 text-white/45">Bạn luôn có người thật để liên hệ khi cần hỗ trợ.</p></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-[#e7e0d0] py-24">
                <div class="mx-auto max-w-4xl px-5 text-center lg:px-8">
                    <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-[#171816] text-[#e2c176]"><Sparkles :size="20" /></div>
                    <h2 class="mt-7 text-4xl font-black tracking-[-.055em] sm:text-6xl">{{ bookingTitle }}</h2>
                    <p class="mx-auto mt-5 max-w-xl text-sm leading-6 text-black/55">{{ bookingHint }}</p>
                    <a href="#booking" class="mt-8 inline-flex items-center gap-2 rounded-full bg-[#171816] px-7 py-4 text-sm font-bold text-white shadow-xl shadow-black/10 transition hover:-translate-y-0.5">Bắt đầu đặt xe <ArrowRight :size="16" /></a>
                </div>
            </section>
        </main>

        <footer class="bg-[#101310] text-white">
            <div class="mx-auto max-w-7xl px-5 py-14 lg:px-8">
                <div class="grid gap-12 md:grid-cols-4">
                    <div class="md:col-span-2"><div class="flex items-center gap-3"><div class="grid size-10 place-items-center rounded-2xl bg-white text-[#171816]"><CarFront :size="20" /></div><div><div class="text-lg font-black tracking-[-.04em]">CARD</div><div class="text-[9px] uppercase tracking-[.28em] text-white/40">Booking</div></div></div><p class="mt-6 max-w-sm text-sm leading-6 text-white/40">Nền tảng đặt xe và thuê xe trực tuyến cho những hành trình đáng nhớ.</p></div>
                    <div><div class="text-xs font-bold uppercase tracking-[.18em] text-white/35">Khám phá</div><div class="mt-5 grid gap-3 text-sm text-white/60"><a href="#services" class="hover:text-white">Dịch vụ</a><a href="#fleet" class="hover:text-white">Đội xe</a><a href="#routes" class="hover:text-white">Lịch trình</a></div></div>
                    <div><div class="text-xs font-bold uppercase tracking-[.18em] text-white/35">Liên hệ</div><div class="mt-5 grid gap-3 text-sm text-white/60"><a href="tel:+84900000000" class="hover:text-white">0900 000 000</a><a href="mailto:hello@cardbooking.vn" class="hover:text-white">hello@cardbooking.vn</a><span>Đà Nẵng, Việt Nam</span></div></div>
                </div>
                <div class="mt-14 flex flex-col justify-between gap-3 border-t border-white/10 pt-6 text-[11px] text-white/30 sm:flex-row"><span>© 2026 Card Booking. All rights reserved.</span><span>Đặt xe đơn giản · Di chuyển thông minh</span></div>
            </div>
        </footer>
    </div>
</template>
