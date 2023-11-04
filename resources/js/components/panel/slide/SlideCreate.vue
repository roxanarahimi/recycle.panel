<template>
    <transition name="route" mode="out-in" appear>
        <section>
            <h3 class="mb-5">ثبت اسلاید جدید</h3>

            <div class="row mt-3">
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="w-100 mb-3">
                                <form id="editForm">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-3 mb-3">
                                            <label for="type" class="mb-2">نوع اسلاید</label>
                                            <select id="type" class="form-select" @change="typeChanged">
                                                <option value="image">تصویر</option>
                                                <option value="video">ویدئو</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label v-if="type == 'image'" class="form-label">تصویر</label><br/>
                                            <label v-if="type == 'video'"  class="form-label">کاور ویدئو</label><br/>
                                            <image-cropper name=""  caption="" :hasCaption="hasCaption" :isRequired="imgRequired" :aspect="aspect"/>
                                            <div id="imageHelp" class="form-text error"></div>
                                        </div>
                                        <div v-if="type == 'video'" class="col-md-6 col-lg-3 mb-3">
                                            <label for="video" class="mb-2">شناسه cloudinary ویدئو</label>
                                            <input type="text" id="video" class="form-control" required>
                                            <div id="videoHelp" class="form-text error"></div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <button class="btn btn-primary" @click.prevent="createInfo" type="submit">
                                                ثبت
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </section>
    </transition>

</template>

<script>
import ImageCropper from '../../components/ImageCropper';
import App from '../App';
import btnSubmit from "../../components/BtnSubmit";
export default {
    components: {ImageCropper, btnSubmit},
    data: function () {
        return {
            id: '',
            blog: [],
            errors: [],
            image_codes: [],
            image_names: [],
            imgRequired: true,
            hasCaption: false,
            aspect: 1920 / 1080,
            type: 'image'
        }
    },
    mounted() {

    },
    methods: {
        async createInfo() {
            await App.methods.checkToken();
            this.errors = [];
            let emptyFieldsCount = 0;
            let req = document.querySelectorAll('[required]');
            req.forEach((element) => {
                if (element.value === "") {
                    element.classList.add('hasError');
                    element.nextSibling.innerHTML = "فیلد اجباری";
                    emptyFieldsCount++;
                } else {
                    element.classList.remove('hasError');
                    element.nextSibling.innerHTML = "";
                }
            });


            if (emptyFieldsCount === 0) {

                let data;
                if (this.type == 'image') {
                    data = {
                        type: this.type,
                        image: document.getElementById('Image__code').value,
                        video: null,

                    }
                } else if (this.type == 'video') {
                    data = {
                        type: this.type,
                        image: document.getElementById('Image__code').value,
                        video: document.getElementById('video').value,
                    }
                }

                await axios.post('/api/panel/slide', data)
                    .then((response) => {
                        if (response.status === 201 || response.status === 200) {
                            setTimeout(() => {
                                this.$router.push({path: '/panel/slides'});
                            }, 1000);

                        }
                    })
                    .catch((error) => {
                        console.log(error);
                        console.log(error.message);
                        console.log(error.data);

                        if (error.status === 422) {
                            let errorList = Array(error.data);
                            for (var i = 0; i < errorList.length; i++) {
                                this.errors = errorList[i];
                            }
                            setTimeout(() => {
                            }, 1000);
                        } else if (error.status === 500) {
                            if (error.data.message.includes("SQLSTATE")) {
                                console.error('خطای پایگاه داده');

                                async function showAlertSql() {
                                    setTimeout(() => {
                                        alert(error.response.data.message);
                                    }, 200);
                                }

                                showAlertSql();
                            } else {
                                async function showAlert500() {
                                    setTimeout(() => {
                                        alert(error.message + ' '
                                            + error.response.data.message);
                                    }, 200);
                                }

                                showAlert500();
                            }
                        } else {

                            async function showAlert() {
                                setTimeout(() => {
                                    alert(error.message);
                                }, 200);
                            }

                            showAlert();
                        }

                    })
            }
        },
        typeChanged() {
            this.type = document.querySelector('#type').value;
        }
    }
}
</script>
<style>
span i {
    cursor: pointer;
}


.nav-tabs .nav-link {
    color: black;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    /*background-color: #F8F9FA;*/
    border-bottom: 0 !important;
}

.btn-primary, .btn-primary:focus, .btn-primary:hover {
    /*background-color: darkcyan;*/
}

</style>
