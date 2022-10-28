const {loadModule} = window['vue2-sfc-loader'];
let vueContainer = []
let formObj = {
    $e: null, finish: false
};

function hook($e) {
    if (!formObj.$e) {
        formObj.$e = $e;
        formObj.$e.submit(function () {
            console.log("submit emitted", 'finish', formObj.finish)
            if (formObj.finish) {
                return true
            }
            for (let v of vueContainer) {
                v.vue.$refs.uploader.submit()
            }
            return false;
        })
    }
}

function emitOssUploadFinish(key) {
    let finish = true;
    for (let v of vueContainer) {
        finish = finish && v.finish
        console.log('------', 'emit finish111')
        $(`input[name='${v.key}']`).val(JSON.stringify(v.files))
    }
    formObj.finish = finish
    console.log('------', 'emit finish2222')
    if (formObj.finish) {
        console.log('all finish')
        formObj.$e.submit()
    }
}

const bootVue = (el, cls) => {
    hook($(el).parents('form'))
    const options = {
        moduleCache: {},
        getFile(url) {
            return fetch(url).then(res => res.text());
        },
        addStyle() { /* unused here */
        },
    }
    loadModule('/vendor/laravel-admin-ext/aliyun-oss-upload/uploader.vue', options).then(uploader => {
        vueContainer.push({
            key: cls,
            finish: false,
            files: [],
            vue: new Vue({
                el, components: {uploader}
            })
        })
    })
};
