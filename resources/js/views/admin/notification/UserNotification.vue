<template>
    <div class="border rounded-2xl shadow-xl p-4">
        <div class="flex flex-row justify-between items-center">
            <h3 class="font-bold text-lg mb-4">Сповіщення</h3>
        </div>

        <div class="flex flex-col">
            <div class="form-control">
                <div class="label">
                    <span class="label-text">Текст сповіщення</span>
                </div>
                <textarea v-model="message" class="textarea textarea-bordered h-24"></textarea>
            </div>

            <button @click="sendNotifications" :disabled="!message" class="btn btn-success rounded-full self-end mt-4">Розіслати сповіщення</button>
        </div>
    </div>
</template>
<script setup>
import {ref} from "vue";
import axios from "axios";
import {toast} from "vue3-toastify";

const message = ref(null);

const sendNotifications = () => {
    axios
        .post('/api/notifications', {
            text: message.value
        })
        .then(() => {
            toast.success('Сповіщення розіслано користувачам');
            message.value = null;
        })
        .catch(() => {
            toast.error('Сталась помилка');
        })
}
</script>
