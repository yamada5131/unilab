<template>
    <Card
        class="cursor-pointer shadow-md hover:shadow-xl"
        @click="$emit('view')"
    >
        <CardHeader>
            <CardTitle>{{ room.name }}</CardTitle>
            <CardDescription>
                Số hàng: {{ room.grid_rows }}, Số cột: {{ room.grid_cols }}
            </CardDescription>
        </CardHeader>
        <CardContent>
            <p>Thông tin chi tiết phòng sẽ hiển thị tại đây.</p>
        </CardContent>
        <!-- TODO: Fix responsive các nút bị hiển thị vượt ra ngoài card -->
        <CardFooter
            class="flex flex-col justify-end gap-2 px-6 pb-6 sm:flex-row sm:gap-x-2"
        >
            <Link :href="route('rooms.show', room.id)">
                <Button class="w-full bg-blue-500 text-white hover:bg-blue-600">
                    Xem Chi Tiết
                </Button>
            </Link>
            <Button
                class="bg-yellow-500 text-white hover:bg-yellow-600"
                @click.stop="openEditDialog"
            >
                Chỉnh sửa
            </Button>
            <!-- TODO: Hiển thị dialog khi bấm nút xóa -->
            <Button variant="destructive" @click.stop="$emit('delete')">
                Xóa
            </Button>
        </CardFooter>
    </Card>

    <!-- Edit Room Dialog -->
    <RoomDialog
        :form-id="`editRoomForm-${room.id}`"
        :is-open="isEditDialogOpen"
        :is-edit="true"
        :room="room"
        @close="isEditDialogOpen = false"
        @update:is-open="isEditDialogOpen = $event"
        @submit="handleEditSubmit"
    />
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { useRoomStore } from '@/stores/room';
import { Room } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import RoomDialog from './RoomDialog.vue';

// Props definition
const props = defineProps<{
    room: Room;
}>();

// Events emitted by this component
defineEmits<{
    view: [];
    delete: [];
}>();

// Local state for the edit dialog
// We keep this local as it's specific to this card instance
const isEditDialogOpen = ref<boolean>(false);

const roomStore = useRoomStore();

// Open the edit dialog
const openEditDialog = () => {
    isEditDialogOpen.value = true;
};

// Handle edit form submission
const handleEditSubmit = (values) => {
    roomStore.updateRoom(props.room.id, values);
    isEditDialogOpen.value = false;
};
</script>
