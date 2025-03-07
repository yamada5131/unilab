// TODO: Đọc lại phần Store này
import { useToast } from '@/components/ui/toast/use-toast';
import { Room } from '@/types';
import { router } from '@inertiajs/vue3';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useRoomStore = defineStore('room', () => {
    const isCreateDialogOpen = ref(false);
    const isEditDialogOpen = ref(false);
    const currentRoom = ref<Partial<Room> | null>(null);
    const { toast } = useToast();

    function openCreateDialog() {
        isCreateDialogOpen.value = true;
    }

    function closeCreateDialog() {
        isCreateDialogOpen.value = false;
    }

    function openEditDialog(room: Room) {
        currentRoom.value = room;
        isEditDialogOpen.value = true;
    }

    function closeEditDialog() {
        isEditDialogOpen.value = false;
        currentRoom.value = null;
    }

    function createRoom(values: any) {
        router.post(route('rooms.store'), values, {
            onSuccess: () => {
                closeCreateDialog();
                toast({
                    title: 'Thành công',
                    description: 'Phòng mới đã được tạo',
                });
            },
            onError: () => {
                toast({
                    title: 'Lỗi',
                    description: 'Có lỗi xảy ra khi tạo phòng',
                    variant: 'destructive',
                });
            },
        });
    }

    function updateRoom(roomId: string, values: any) {
        router.put(route('rooms.update', roomId), values, {
            onSuccess: () => {
                closeEditDialog();
                toast({
                    title: 'Thành công',
                    description: 'Thông tin phòng đã được cập nhật',
                });
            },
            onError: () => {
                toast({
                    title: 'Lỗi',
                    description: 'Có lỗi xảy ra khi cập nhật thông tin phòng',
                    variant: 'destructive',
                });
            },
        });
    }

    function deleteRoom(roomId: string) {
        if (confirm('Bạn có chắc muốn xóa phòng này?')) {
            router.delete(route('rooms.destroy', roomId), {
                onSuccess: () => {
                    toast({
                        title: 'Thành công',
                        description: 'Phòng đã được xóa',
                    });
                },
                onError: () => {
                    toast({
                        title: 'Lỗi',
                        description: 'Có lỗi xảy ra khi xóa phòng',
                        variant: 'destructive',
                    });
                },
            });
        }
    }

    return {
        isCreateDialogOpen,
        isEditDialogOpen,
        currentRoom,
        openCreateDialog,
        closeCreateDialog,
        openEditDialog,
        closeEditDialog,
        createRoom,
        updateRoom,
        deleteRoom,
    };
});
