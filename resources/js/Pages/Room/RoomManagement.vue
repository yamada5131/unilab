<template>
    <div>
        <button @click="openAddRoomModal">Thêm phòng máy</button>

        <div v-for="room in rooms" :key="room.id">
            <p>{{ room.name }} - {{ room.capacity }} người</p>
            <button @click="editRoom(room)">Sửa</button>
            <button @click="deleteRoom(room.id)">Xóa</button>
        </div>

        <add-room-modal v-if="showAddRoomModal" @close="closeAddRoomModal" />
    </div>
</template>

<script setup>
import { ref } from "vue";
import AddRoomModal from "./AddRoomModal.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({});

const props = defineProps({
    rooms: {
        type: Array,
        required: true,
    },
});

const showAddRoomModal = ref(false);

const openAddRoomModal = () => {
    showAddRoomModal.value = true;
};

const closeAddRoomModal = () => {
    showAddRoomModal.value = false;
};

const deleteRoom = (id) => {
    if (confirm("Are you sure you want to move this to trash")) {
        form.delete(route("rooms.destroy", { id: id }), {
            preserveScroll: true,
        });
    }
};

const editRoom = (room) => {
    useInertia().visit(`/rooms/${room.id}/edit`);
};
</script>
