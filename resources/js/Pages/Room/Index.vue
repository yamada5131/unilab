<template>
    <div class="p-6">
        <!--* Thanh tìm kiếm và nút Thêm Phòng -->
        <div class="mb-6 flex items-center justify-between">
            <!--* Search input -->
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
            <Button
                class="bg-blue-500 text-white hover:bg-blue-600"
                @click="isCreateDialogOpen = true"
            >
                Thêm Phòng
            </Button>

            <RoomDialog
                form-id="createRoomForm"
                :is-open="isCreateDialogOpen"
                :is-edit="false"
                @close="isCreateDialogOpen = false"
                @update:is-open="isCreateDialogOpen = $event"
                @submit="handleCreateSubmit"
            />
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
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { toast } from '@/components/ui/toast';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Room } from '@/types';
import { router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { computed, h, ref } from 'vue';
import RoomCard from './Partials/RoomCard.vue';
import RoomDialog from './Partials/RoomDialog.vue';

const isCreateDialogOpen = ref(false);

// State dùng cho tìm kiếm, modal và form
const searchQuery = ref<string>('');

const props = defineProps<{
    rooms: { data: Room[] };
}>();

defineOptions({
    layout: AuthenticatedLayout,
});

const handleCreateSubmit = (values) => {
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
            isCreateDialogOpen.value = false;
            toast({
                title: 'Thành công',
                description: 'Phòng đã được tạo mới',
            });
        },
    });
};

// Functions for action buttons
const viewRoomDetails = (roomId) => {
    router.get(route('rooms.show', roomId));
};

const editRoom = (room) => {
    console.log(`Edit room ${room.id}`);
    // Implementation for edit functionality
};

const deleteRoom = (roomId) => {
    router.delete(route('rooms.destroy', roomId));
};

// Computed property lọc danh sách phòng theo từ khóa
const filteredRooms = computed(() => {
    if (!searchQuery.value) return props.rooms.data;
    return props.rooms.data.filter((room) =>
        room.name.toLowerCase().includes(searchQuery.value.toLowerCase()),
    );
});
</script>

<style scoped>
/* Bạn có thể tuỳ chỉnh thêm CSS theo phong cách của dự án */
</style>
