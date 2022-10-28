<template>
    <div>
        <el-upload
            action="#"
            list-type="picture-card"
            :auto-upload="false"
            :file-list="list"
        >
            <i slot="default" class="el-icon-plus"></i>
            <div slot="file" slot-scope="{file}">
                <img class="el-upload-list__item-thumbnail" :src="file.url">
                <span class="el-upload-list__item-actions">
                <span class="el-upload-list__item-preview" v-on:click="handlePreview(file)">
                  <i class="el-icon-zoom-in"></i>
                </span>
                <span
                    v-if="!disabled"
                    class="el-upload-list__item-delete"
                    v-on:click="handleDownload(file)"
                ><i class="el-icon-download"></i></span>
                <span
                    v-if="!disabled"
                    class="el-upload-list__item-delete"
                    v-on:click="handleRemove(file)"
                ><i class="el-icon-delete"></i></span>
      </span>
            </div>
        </el-upload>
        <el-dialog :visible.sync="dialogVisible">
            <img width="100%" :src="dialogImageUrl" alt="">
        </el-dialog>
    </div>
</template>

<script>
export default {
    prop: {
        key: String,
        path: String
    },
    data() {
        return {
            dialogImageUrl: '',
            dialogVisible: false,
            disabled: false,
            list: []
        };
    },
    methods: {
        handleRemove(file) {
            console.log(file);
        },
        handlePreview(file) {
            this.dialogImageUrl = file.url;
            this.dialogVisible = true;
        },
        handleDownload(file) {
            console.log(file);
        }
    }
}
</script>

<style scoped>

</style>