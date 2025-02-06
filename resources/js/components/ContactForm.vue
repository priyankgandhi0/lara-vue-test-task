<template>
    <div class="card w-50 d-flex m-auto shadow-lg rounded-lg">
        <div class="card-header text-center bg-primary text-white">
            <h3><b>Contact Us</b></h3>
        </div>
        <div class="card-body p-4">
            <!-- Message Feedback -->
            <div
                v-if="message"
                :class="{
                    alert: true,
                    'alert-success': success === true,
                    'alert-danger': success === false,
                }"
                role="alert"
            >
                {{ message }}
            </div>
            <form @submit.prevent="submitForm">
                <!-- Form Fields -->
                <div class="mb-3">
                    <label for="name" class="form-label">Name:</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="form-control"
                        placeholder="Enter your full name"
                        required
                    />
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="form-control"
                        placeholder="Enter your email"
                        required
                    />
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone:</label>
                    <input
                        id="phone"
                        v-model="form.phone"
                        type="text"
                        class="form-control"
                        placeholder="Enter your phone number"
                        required
                    />
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message:</label>
                    <textarea
                        id="message"
                        v-model="form.message"
                        class="form-control"
                        placeholder="Write your message here"
                        rows="4"
                        required
                    ></textarea>
                </div>
                <div class="mb-3">
                    <label for="street" class="form-label">Street:</label>
                    <input
                        id="street"
                        v-model="form.street"
                        type="text"
                        class="form-control"
                        placeholder="Enter your street address"
                        required
                    />
                </div>
                <div class="mb-3">
                    <label for="state" class="form-label">State:</label>
                    <input
                        id="state"
                        v-model="form.state"
                        type="text"
                        class="form-control"
                        placeholder="Enter your state"
                        required
                    />
                </div>
                <div class="mb-3">
                    <label for="zip" class="form-label">Zip:</label>
                    <input
                        id="zip"
                        v-model="form.zip"
                        type="text"
                        class="form-control"
                        placeholder="Enter your zip code"
                        required
                    />
                </div>
                <div class="mb-3">
                    <label for="country" class="form-label">Country:</label>
                    <input
                        id="country"
                        v-model="form.country"
                        type="text"
                        class="form-control"
                        placeholder="Enter your country"
                        required
                    />
                </div>

                <!-- Image Upload -->
                <div class="row new_box mt-9">
                    <div class="d-flex box-item col-lg-12 px-1 col-md-6 col-12">
                        <div class="fv-row w-100">
                            <label for="image" class="form-label"
                                >Images (JPG):</label
                            >
                            <input
                                id="image"
                                type="file"
                                accept=".jpg"
                                class="form-control"
                                multiple
                                @change="handleImageUpload"
                                ref="imageInput"
                            />
                        </div>
                    </div>

                    <div id="uploaded_img"></div>
                </div>

                <!-- File Upload -->
                <div class="row new_box mt-3">
                    <div class="d-flex box-item col-lg-12 px-1 col-md-6 col-12">
                        <div class="fv-row w-100">
                            <label for="file" class="form-label"
                                >Files (PDF):</label
                            >
                            <input
                                id="file"
                                type="file"
                                accept=".pdf"
                                class="form-control"
                                multiple
                                @change="handleFileUpload"
                                ref="fileInput"
                            />
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 mt-4">
                    Submit
                </button>
            </form>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { ref } from "vue";

const imgs = ref([]);

export default {
    data() {
        return {
            form: {
                name: "",
                email: "",
                phone: "",
                message: "",
                street: "",
                state: "",
                zip: "",
                country: "",
                images: [],
                files: [],
            },
            message: "",
            success: null,
        };
    },
    methods: {
        handleImageUpload(event) {
            this.form.images.push(event.target.files[0]);
        },
        handleFileUpload(event) {
            this.form.files = Array.from(event.target.files);
        },
        async submitForm() {
            console.log(this.form, "form");
            try {
                if (
                    this.form.images.some((image) => !this.isValidImage(image))
                ) {
                    throw new Error(
                        "Invalid image file. Only JPG images are allowed."
                    );
                }
                if (this.form.files.some((file) => !this.isValidFile(file))) {
                    throw new Error(
                        "Invalid file type. Only PDF files are allowed."
                    );
                }

                const formData = new FormData();
                for (const key in this.form) {
                    if (Array.isArray(this.form[key])) {
                        this.form[key].forEach((item) => {
                            formData.append(`${key}[]`, item);
                        });
                    } else {
                        formData.append(key, this.form[key]);
                    }
                }

                const response = await axios.post("/submit", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });

                console.log(response.data.msg);
                if (response.data.status === 0) {
                    this.success = false;
                    this.message = response.data.msg;
                } else {
                    this.success = true;
                    this.message = response.data.msg;
                    this.resetForm();
                }
            } catch (error) {
                this.success = false;
                if (error.response && error.response.data.msg) {
                    this.message = error.response.data.msg;
                } else {
                    this.message =
                        error.message || "Submission failed due to an error.";
                }
            }
        },

        isValidImage(image) {
            return (
                image &&
                (image.type === "image/jpeg" || image.type === "image/jpg")
            );
        },

        isValidFile(file) {
            return file && file.type === "application/pdf";
        },

        resetForm() {
            this.form = {
                name: "",
                email: "",
                phone: "",
                message: "",
                street: "",
                state: "",
                zip: "",
                country: "",
                images: [],
                files: [],
            };
            this.success = null;
            this.message = "";

            if (this.$refs.imageInput) {
                this.$refs.imageInput.value = "";
            }
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = "";
            }
        },

        addNewBoxItem(button) {
            const originalBoxItem = button.closest(".box-item");
            const subBox = originalBoxItem.cloneNode(true);

            const plusButton = subBox.querySelector(".plus");
            plusButton.classList.remove("plus", "btn-success");
            plusButton.classList.add("minuse", "btn-danger");
            plusButton.textContent = "-";

            const label = subBox.querySelector(".label");
            if (label) {
                label.textContent = "";
                label.classList.remove("required");
                label.classList.add("mt-4");
            }

            const inputs = subBox.querySelectorAll("input");
            inputs.forEach((input) => {
                input.value = "";
            });

            button.closest(".new_box").appendChild(subBox);
        },
    },
    mounted() {
        let count = 0;
        document.addEventListener("click", (event) => {
            if (event.target.matches(".plus")) {
                if (count < 10) {
                    this.addNewBoxItem(event.target);
                    count++;
                }
            } else if (event.target.matches(".minuse")) {
                event.target.closest(".box-item").remove();
                count--;
            }
        });
    },
};
</script>

<style scoped>
.success {
    color: green;
}
.error {
    color: red;
}
</style>
