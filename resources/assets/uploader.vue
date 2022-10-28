<template>
    <div>
        <van-uploader
            :after-read="fileChange"
            v-model="fileList"
            name="file"
            accept="*/*"
            @click-preview="handlePreview"
            :preview-options="{closeable:true}"
            result-type="file"
        />
        <van-popup v-model="dialogVisible" :style="{ width: '700px'}" round closeable>
            <video width="100% " :src="dialogFile.url" controls v-if="dialogFile.fileType === 'video'"></video>
        </van-popup>
    </div>
</template>

<script>
const FileTypes = {
    'video': ['mp4', 'm4v'],
    'image': ['jpg', 'jpeg', 'png', 'gif', 'bmp'],
    'audio': ['mp3', 'ogg']
}
export default {
    props: {
        field: String,
        path: String,
        files: Array
    },
    data() {
        return {
            dialogImageUrl: '',
            dialogVisible: false,
            disabled: false,
            action: '',
            fileList: [],
            dialogFile: {},
            uploadData: {}
        };
    },
    mounted() {
        if (this.files.length > 0) {
            this.prepareFileList()
        }
    },
    methods: {
        log(e) {
            console.log(e)
        },
        filename(path) {
            return path.split('/').at(-1)
        },
        handlePreview(file) {
            if (file.fileType === 'image' || file.fileType === 'other') {
                return
            }
            this.dialogFile = file;
            this.dialogVisible = true;
        },
        fileChange(file) {
            file.fileType = this.guessType(file.file.name)
            if (!file.url) {
                file.url = this.getObjectURL(file.file)
            }
        },
        beforeUpload(file) {
            return new Promise((resolve, reject) => {
                $.post(ALI_UPLOADER_URL, {
                    name: file.file.name,
                    path: this.path,
                    _token: LA.token
                }, (res, status) => {
                    if (status === 'error') {
                        reject()
                        return
                    }
                    let formData = new FormData()
                    formData.append('key', res.filename)
                    formData.append('policy', res.policy)
                    formData.append('OSSAccessKeyId', res.accessid)
                    formData.append('success_action_status', '200')
                    formData.append('signature', res.signature)
                    resolve({
                        host: res.host, formData: formData
                    })
                }, 'json')
            })
        },
        upload(host, formData, file) {
            return new Promise((resolve) => {
                formData.append('file', file.file)
                console.log(formData)
                $.ajax({
                    type: 'POST',
                    url: host,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: () => {
                        file.status = 'done';
                        file.message = '上传成功';
                        file.path = formData.get('key')
                        resolve(file)
                    },
                    err: (error) => {
                        console.log('upload fail', error)
                        file.status = 'failed';
                        file.message = '上传失败';
                        resolve()
                    }
                })
            })

        },
        guessType(name) {
            const ext = name.split('.').at(-1)
            for (let t in FileTypes) {
                if (FileTypes[t].indexOf(ext) !== -1) {
                    return t
                }
            }
            return 'other'
        },
        async submit() {
            let hasError = false
            for (let file of this.fileList) {
                if (file.path) {
                    continue;
                }
                file.status = 'uploading'
                file.message = '上传中'
                try {
                    let {host, formData} = await this.beforeUpload(file)
                    const af = await this.upload(host, formData, file)
                    if (af) {
                        file = af
                    }
                } catch (e) {
                    hasError = true
                }
            }
            if (!hasError) {
                this.finish()
            } else {
                Toast.fail('部分文件上传失败')
            }

        },
        getObjectURL(file) {
            let url = null;
            if (window.createObjectURL !== undefined) { // basic
                url = window.createObjectURL(file);
            } else if (window.URL !== undefined) { // mozilla(firefox)
                url = window.URL.createObjectURL(file);
            } else if (window.webkitURL !== undefined) { // webkit or chrome
                url = window.webkitURL.createObjectURL(file);
            }
            return url;
        },
        finish() {
            console.log(this.field, '上传完成')
            for (let v of vueContainer) {
                if (v.key === this.field) {
                    v.finish = true
                    v.files = this.fileList.map(f => f.path)
                }
            }
            window.emitOssUploadFinish(this.field)
        },
        prepareFileList() {
            for (const f of this.files) {
                const file = {
                    status: 'done',
                    message: '上传成功',
                    path: f.path,
                    url: f.url,
                    fileType: this.guessType(f.url),
                    file: {name: this.filename(f.path)}
                }
                this.fileList.push(file)
            }
        }
    }
}
</script>