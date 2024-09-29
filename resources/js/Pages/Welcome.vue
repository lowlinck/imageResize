<template>
    <div>
        <h1>Upload Images</h1>
        <form @submit.prevent="uploadImages" enctype="multipart/form-data">
            <input type="file" multiple @change="handleFiles" accept="image/*" />
            <button type="submit" :disabled="images.length === 0">Upload</button>
        </form>

        <div v-for="(image, index) in images" :key="index">
            <p>{{ image.name }}</p>
            <div v-if="uploadProgress[image.name]">
                <progress :value="uploadProgress[image.name]" max="100"></progress>
                <span>{{ uploadProgress[image.name] }}%</span>
            </div>
            <div v-if="taskStatuses[taskIds[index]]">
                <progress :value="taskStatuses[taskIds[index]].progress" max="100"></progress>
                <span>{{ taskStatuses[taskIds[index]].progress }}%</span>
                <p>Status: {{ taskStatuses[taskIds[index]].status }}</p>
            </div>
        </div>
    </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';
import { ref } from 'vue';
import axios from 'axios';

export default {
    props: {
        taskIdsFromSession: Array,
    },
    setup(props) {
        const images = ref([]);
        const uploadProgress = ref({});
        const taskIds = ref(props.taskIdsFromSession || []);
        const taskStatuses = ref({});

        const handleFiles = (event) => {
            const selectedFiles = Array.from(event.target.files);
            if (selectedFiles.length > 10) {
                alert('You can upload up to 10 images.');
                return;
            }
            images.value = selectedFiles;
        };

        const uploadImages = () => {
            const formData = new FormData();
            images.value.forEach((image) => {
                formData.append('images[]', image);
            });

            axios.post('/upload-images', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                onUploadProgress: (progressEvent) => {
                    const percentCompleted = Math.round(
                        (progressEvent.loaded * 100) / progressEvent.total
                    );
                    images.value.forEach((image) => {
                        uploadProgress.value[image.name] = percentCompleted;
                    });
                },
            })
                .then(response => {
                    taskIds.value = response.data.task_ids || [];
                    pollTaskStatuses();
                })
                .catch(error => {
                    console.error('Upload failed:', error);
                });
        };

        const pollTaskStatuses = () => {
            if (taskIds.value.length === 0) return;

            const interval = setInterval(async () => {
                try {
                    const response = await axios.get('/tasks/statuses', {
                        params: {
                            ids: taskIds.value.join(','),
                        },
                    });

                    response.data.tasks.forEach((task) => {
                        taskStatuses.value[task.id] = {
                            status: task.status,
                            progress: task.progress,
                        };
                    });

                    const allCompleted = response.data.tasks.every(
                        (task) => task.status === 'success' || task.status === 'failed'
                    );

                    if (allCompleted) {
                        clearInterval(interval);
                    }
                } catch (error) {
                    console.error(error);
                    clearInterval(interval);
                }
            }, 3000);
        };

        return {
            images,
            uploadProgress,
            taskIds,
            taskStatuses,
            handleFiles,
            uploadImages,
            pollTaskStatuses,
        };
    },
    mounted() {
        if (this.taskIds.length > 0) {
            this.pollTaskStatuses();
        }
    },
};
</script>
