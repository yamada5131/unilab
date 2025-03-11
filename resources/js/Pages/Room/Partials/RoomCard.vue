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
                @click.stop="handleOpenEditDialog"
            >
                Chỉnh sửa
            </Button>
            <AlertDialog
                :open="
                    roomStore.isDeleteDialogOpen &&
                    roomStore.currentRoom?.id === room.id
                "
            >
                <AlertDialogTrigger as-child>
                    <Button
                        variant="destructive"
                        @click.stop="handleOpenDeleteDialog"
                    >
                        Xóa
                    </Button>
                </AlertDialogTrigger>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle
                            >Are you absolutely sure?</AlertDialogTitle
                        >
                        <AlertDialogDescription>
                            This action cannot be undone. This will permanently
                            delete your room and remove its data from our
                            servers.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel
                            @click="roomStore.closeDeleteDialog()"
                            >Cancel</AlertDialogCancel
                        >
                        <AlertDialogAction @click.stop="handleDeleteRoom">
                            Continue
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </CardFooter>
    </Card>

    <!-- Edit Room Dialog -->
    <RoomDialog
        :form-id="`editRoomForm-${room.id}`"
        :is-open="
            roomStore.isEditDialogOpen && roomStore.currentRoom?.id === room.id
        "
        :is-edit="true"
        :room="room"
        @close="roomStore.closeEditDialog()"
        @update:is-open="updateEditDialogState"
        @submit="handleEditRoom"
    />
</template>

<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
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
import RoomDialog from './RoomCreateUpdateDialog.vue';

// Props definition
const props = defineProps<{
    room: Room;
}>();

// Events emitted by this component
defineEmits<{
    view: [];
    delete: [];
}>();

const roomStore = useRoomStore();

// Open the edit dialog
const handleOpenEditDialog = () => {
    roomStore.openEditDialog(props.room);
};

// Handle edit form submission
const handleEditRoom = (values) => {
    roomStore.updateRoom(props.room.id, values);
};

const updateEditDialogState = (isOpen: boolean) => {
    if (!isOpen) {
        roomStore.closeEditDialog();
    }
};

const handleOpenDeleteDialog = () => {
    roomStore.openDeleteDialog(props.room);
};

const handleDeleteRoom = () => {
    roomStore.deleteRoom(props.room.id);
};
</script>
