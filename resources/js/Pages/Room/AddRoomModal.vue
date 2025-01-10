<!-- AddRoomModal.vue -->
<template>
    <div class="modal">
        <div class="modal-content">
            <h3>Thêm phòng máy</h3>
            <form @submit.prevent="submitForm">
                <div>
                    <label for="name">Tên phòng:</label>
                    <input id="name" v-model="form.name" type="text" required />
                </div>
                <div>
                    <label for="capacity">Sức chứa:</label>
                    <input
                        id="capacity"
                        v-model="form.capacity"
                        type="number"
                        required
                    />
                </div>
                <button type="submit">Lưu</button>
                <button @click="closeModal">Hủy</button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const emit = defineEmits;

const form = ref({
    name: "",
    capacity: "",
});

const closeModal = () => {
    emit("close");
};

const submitForm = () => {
    emit("submit", { ...form.value });
    closeModal();
};
</script>

<style scoped>
.modal {
    /* Basic modal styling */
}
.modal-content {
    /* Modal content styling */
}
</style>
