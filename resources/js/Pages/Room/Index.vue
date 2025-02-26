<template>
    <div class="p-6">
        <!-- Thanh tìm kiếm và nút Thêm Phòng -->
        <div class="mb-6 flex items-center justify-between">
            <div class="relative w-full max-w-sm items-center">
                <Input
                    id="search"
                    v-model="searchQuery"
                    type="text"
                    placeholder="Tìm phòng theo tên..."
                    class="pl-10"
                />
                <span
                    class="absolute inset-y-0 start-0 flex items-center justify-center px-2"
                >
                    <Search class="size-6 text-muted-foreground" />
                </span>
            </div>

            <!--* Form thêm mới phòng -->
            <Dialog v-model:open="isDialogOpen">
                <DialogTrigger as-child>
                    <Button
                        class="bg-blue-500 text-white hover:bg-blue-600"
                        @click="isDialogOpen = true"
                    >
                        Thêm Phòng
                    </Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Thêm Phòng Mới</DialogTitle>
                        <DialogDescription>
                            Nhập thông tin phòng mới vào form bên dưới và nhấn
                            Lưu khi hoàn tất.
                        </DialogDescription>
                    </DialogHeader>

                    <form
                        id="dialogForm"
                        @submit.prevent="onSubmit"
                        class="space-y-4"
                    >
                        <FormField
                            v-slot="{ componentField }"
                            name="name"
                            :validate-on-blur="!isFieldDirty"
                        >
                            <FormItem>
                                <FormLabel>Tên Phòng</FormLabel>
                                <FormControl>
                                    <Input
                                        type="text"
                                        placeholder="Nhập tên phòng..."
                                        v-bind="componentField"
                                    />
                                </FormControl>
                                <FormDescription>
                                    Đây là tên hiển thị của phòng trong hệ
                                    thống.
                                </FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField
                            v-slot="{ componentField }"
                            name="grid_rows"
                            :validate-on-blur="!isFieldDirty"
                        >
                            <FormItem>
                                <FormLabel>Số hàng</FormLabel>
                                <FormControl>
                                    <Input
                                        type="number"
                                        placeholder="Nhập số hàng..."
                                        v-bind="componentField"
                                    />
                                </FormControl>
                                <FormDescription>
                                    Số hàng trong sơ đồ bố trí máy tính của
                                    phòng.
                                </FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField
                            v-slot="{ componentField }"
                            name="grid_cols"
                            :validate-on-blur="!isFieldDirty"
                        >
                            <FormItem>
                                <FormLabel>Số cột</FormLabel>
                                <FormControl>
                                    <Input
                                        type="number"
                                        placeholder="Nhập số cột..."
                                        v-bind="componentField"
                                    />
                                </FormControl>
                                <FormDescription>
                                    Số cột trong sơ đồ bố trí máy tính của
                                    phòng.
                                </FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </form>

                    <DialogFooter>
                        <Button type="submit" form="dialogForm">Lưu</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>

        <!-- Danh sách phòng dạng Grid -->
        <div class="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-4">
            <!-- TODO: Chuyển đến trang chi tiết phòng -->
            <RoomCard
                v-for="room in filteredRooms"
                :key="room.id"
                :room="room"
                @view="viewRoomDetails(room.id)"
                @edit="editRoom(room)"
                @delete="deleteRoom(room.id)"
            />
        </div>

        <!-- Show a message when no rooms match the search -->
        <div
            v-if="filteredRooms.length === 0"
            class="mt-8 text-center text-gray-500"
        >
            Không tìm thấy phòng nào phù hợp với từ khóa tìm kiếm
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/Components/ui/dialog';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/Components/ui/form';

import { Input } from '@/Components/ui/input';
import { toast } from '@/Components/ui/toast';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { Search } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed, h, ref } from 'vue';
import * as z from 'zod';
import RoomCard from './Components/RoomCard.vue';

const formSchema = toTypedSchema(
    z.object({
        name: z
            .string()
            .min(2, 'Tên phòng phải có ít nhất 2 ký tự')
            .max(50, 'Tên phòng không được quá 50 ký tự'),
        grid_rows: z
            .number()
            .int()
            .min(1, 'Số hàng phải lớn hơn 0')
            .max(20, 'Số hàng không được vượt quá 20'),
        grid_cols: z
            .number()
            .int()
            .min(1, 'Số cột phải lớn hơn 0')
            .max(20, 'Số cột không được vượt quá 20'),
    }),
);

const { isFieldDirty, handleSubmit } = useForm({
    validationSchema: formSchema,
    initialValues: {
        name: '',
        grid_rows: 5,
        grid_cols: 5,
    },
});

const isDialogOpen = ref(false);

const onSubmit = handleSubmit((values) => {
    toast({
        title: 'You submitted the following values:',
        description: h(
            'pre',
            { class: 'mt-2 w-[340px] rounded-md bg-slate-950 p-4' },
            h('code', { class: 'text-white' }, JSON.stringify(values, null, 2)),
        ),
    });
    router.post(route('rooms.store'), values, {
        onSuccess: () => {
            isDialogOpen.value = false;
        },
    });
});

// State dùng cho tìm kiếm, modal và form
const searchQuery = ref('');

const props = defineProps({
    rooms: { type: Object, required: true },
});

defineOptions({
    layout: AuthenticatedLayout,
});

// Computed property lọc danh sách phòng theo từ khóa
const filteredRooms = computed(() => {
    if (!searchQuery.value) return props.rooms;
    return props.rooms.filter((room) =>
        room.name.toLowerCase().includes(searchQuery.value.toLowerCase()),
    );
});

// Functions for action buttons
const viewRoomDetails = (roomId) => {
    // Navigate to room detail page
    console.log(`View room ${roomId}`);
    // router.push({ name: 'room.show', params: { id: roomId }});
};

const editRoom = (room) => {
    console.log(`Edit room ${room.id}`);
    // Implementation for edit functionality
};

const deleteRoom = (roomId) => {
    console.log(`Delete room ${roomId}`);
    // Implementation for delete functionality
    // Consider adding a confirmation dialog here
};
</script>

<style scoped>
/* Bạn có thể tuỳ chỉnh thêm CSS theo phong cách của dự án */
</style>
