<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Video Upload</h1>
        <div class="border-dashed border-2 border-gray-300 p-6 rounded-lg text-center" @dragover.prevent
            @drop.prevent="handleDrop">
            <input type="file" ref="fileInput" @change="handleFileSelect" class="hidden" accept="video/*" />
            <button @click="$refs.fileInput.click()"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Select Video
            </button>
            <p class="mt-2">or drag and drop your video here</p>
        </div>
        <div v-if="uploadProgress > 0" class="mt-4">
            <div class="bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: `${uploadProgress}%` }"></div>
            </div>
            <p class="mt-2">Upload progress: {{ uploadProgress }}%</p>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

export default {
    setup() {
        const fileInput = ref(null)
        const uploadProgress = ref(0)
        const form = useForm({
            file: null,
        })

        const handleFileSelect = (event) => {
            const file = event.target.files[0]
            if (file) {
                uploadFile(file)
            }
        }

        const handleDrop = (event) => {
            const file = event.dataTransfer.files[0]
            if (file) {
                uploadFile(file)
            }
        }

        const uploadFile = (file) => {
            const chunkSize = 1024 * 1024 // 1MB chunks
            let start = 0
            let chunk = 0
            const totalChunks = Math.ceil(file.size / chunkSize)

            const uploadChunk = () => {
                const end = Math.min(start + chunkSize, file.size)
                const chunkBlob = file.slice(start, end)

                const formData = new FormData()
                formData.append('file', chunkBlob, file.name)
                formData.append('chunk', chunk)
                formData.append('totalChunks', totalChunks)

                form.post('/upload', formData, {
                    preserveState: true,
                    preserveScroll: true,
                    forceFormData: true,
                    onProgress: (progress) => {
                        uploadProgress.value = Math.round((chunk / totalChunks) * 100)
                    },
                    onSuccess: () => {
                        chunk++
                        start = end
                        if (start < file.size) {
                            uploadChunk()
                        } else {
                            uploadProgress.value = 100
                        }
                    },
                })
            }

            uploadChunk()
        }

        return {
            fileInput,
            uploadProgress,
            handleFileSelect,
            handleDrop,
        }
    },
}
</script>