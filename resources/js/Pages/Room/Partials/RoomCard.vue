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
                @click.stop="isEditDialogOpen = true"
            >
                Chỉnh sửa
            </Button>
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
import { toast } from '@/components/ui/toast';
import { Room } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import RoomDialog from './RoomDialog.vue';

// Dialog state
const isEditDialogOpen = ref<boolean>(false);

// Props definition
const props = defineProps<{
    room: Room;
}>();

// Events emitted by this component
const emit = defineEmits<{
    view: [];
    edit: [];
    delete: [];
}>();

// Handle edit form submission
const handleEditSubmit = (values) => {
    router.put(route('rooms.update', props.room.id), values, {
        onSuccess: () => {
            isEditDialogOpen.value = false;
            toast({
                title: 'Thành công',
                description: 'Thông tin phòng đã được cập nhật',
            });
            emit('edit');
        },
        onError: (errors) => {
            toast({
                title: 'Lỗi',
                description: 'Có lỗi xảy ra khi cập nhật thông tin phòng',
                variant: 'destructive',
            });
        },
    });
};
</script>
