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
                @click="roomStore.openCreateDialog()"
            >
                Thêm Phòng
            </Button>

            <RoomDialog
                form-id="createRoomForm"
                :is-open="roomStore.isCreateDialogOpen"
                :is-edit="false"
                @close="roomStore.closeCreateDialog()"
                @update:is-open="roomStore.isCreateDialogOpen = $event"
                @submit="roomStore.createRoom"
            />
        </div>

        <!-- Danh sách phòng dạng Grid -->
        <div class="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-4">
            <RoomCard
                v-for="room in filteredRooms"
                :key="room.id"
                :room="room"
                @view="viewRoomDetails(room.id)"
                @delete="roomStore.deleteRoom(room.id)"
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
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { useRoomStore } from '@/stores/room';
import { Room } from '@/types';
import { router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import RoomCard from './Partials/RoomCard.vue';
import RoomDialog from './Partials/RoomDialog.vue';

// State dùng cho tìm kiếm
const searchQuery = ref<string>('');

const props = defineProps<{
    rooms: { data: Room[] };
}>();

defineOptions({
    layout: AuthenticatedLayout,
});

const roomStore = useRoomStore();

// Function for viewing room details
const viewRoomDetails = (roomId: string) => {
    router.get(route('rooms.show', roomId));
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
