<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <title>Alpine CRUD Application</title>
    <!-- Styles -->
    @vite(['resources/js/app.js'])
</head>

<body>
    <div class="container d-flex justify-content-start mt-5 " x-data="companyCrud()" x-init="getAllCompanies()">
        <div class=" col-4 p-5">
            <div class="card">
                <div class="card-header text-center">
                    <template x-if="isEditMode"><button class="btn btn-info d-float float-end text-white"
                            @click="createCompany()">Create</button></template>
                    <span x-show="! isEditMode">Create</span>
                    <span x-show="isEditMode">Edit</span>
                </div>
                <div class="card-body">
                    <template x-if="error">
                        <div class="alert alert-warning">
                            <span x-text="nameErr"></span>
                        </div>
                    </template>
                    
                    <form @submit.prevent="createCompany()" id="form">
                        <div class="form-group">
                            <label for="name" class="fw-semibold">Name</label>
                            <input type="text" name="name" x-model="company.name" class="form-control"
                                placeholder="Enter name" @keyup.enter="createCompany()" />
                            <p class="text-danger" x-text="errors.name"></p>
                        </div>
                        <template x-if="url">
                            <img :src="url" class="img-fluid" alt="company image" width="100px"
                                height="100px" />
                        </template>
                        <div class="form-group">
                            <label for="formFile" class="form-label fw-semibold">Choose Image</label>
                            <input class="form-control" type="file" name="image" id="image" placeholder="Enter image"
                                @change="fileChange" />
                            <p class="text-danger" x-text="errors.image"></p>
                        </div>
                        <button class="btn btn-primary" type="submit">
                            Save
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-8 mt-5">
            <div class="card">
                <div class="card-header bg-info">
                    Company List
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead class="thead">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="company in companies" :key= "company.id">
                                <tr>
                                    <td x-text="company.id"></td>
                                    <td x-text="company.name"></td>
                                    <td>
                                        <img :src= "company.image" height="100px" width="100px"/>
                                    </td>
                                    <td>
                                        <button @click.prevent="editCompany(company)"
                                            class="btn btn-success">Edit</button>
                                        <button @click.prevent="removeCompany(company.id)"
                                            class="btn btn-danger">Delete</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function companyCrud() {
            return {
                url: '',
                error: false,
                errors: {},
                isEditMode: false,
                index: null,
                company: {
                    id: null,
                    name: ''
                },
                companies: [],

                createCompany() {
                    this.isEditMode = false;
                    this.url = ''
                    this.company.id = null;
                    this.company.name = null;
                    this.errors = {};
                },
                
                createCompany() {
                    this.errors = {};
                    if (!this.isEditMode) {
                        let form = document.getElementById('form');
                        let formData = new FormData(form);
                        axios.post('http://localhost:8000/api/company', formData
                        ).then(response => {
                            this.companies.unshift(response.data);
                            this.error = false;
                            this.nameErr = '';
                            this.url = '',
                                form.reset()
                        }).catch(error => {
                            this.errors = error.response.data.errors;
                        });
                    } else {
                        let form = document.getElementById('form');
                        let formData = new FormData(form);

                        axios.post('http://localhost:8000/api/company/edit/' + this.company.id, formData, {
                            headers: {
                                "Content-Type": "multipart/form-data",
                            }
                        }).then(response => {
                            let company = response.data;
                            this.error = false;
                            this.nameErr = '';
                            this.url = '';
                            this.isEditMode = false;
                            this.companies = this.companies.map(company => {
                                if (company.id == company.id) {
                                    return company;
                                } else {
                                    return company;
                                }
                            })
                            form.reset()
                        }).catch(error => {
                            console.log(error.response)
                            this.errors = error.response.data.errors;
                        });
                    }
                },

                editCompany(company) {
                    this.isEditMode = true;
                    this.url = company.image
                    this.company.id = company.id;
                    this.company.name = company.name;
                    this.errors = {};
                },

                getAllCompanies() {
                    axios.get('http://127.0.0.1:8000/api/company/')
                        .then(response => {
                            this.companies = response.data
                        });
                },

                removeCompany(id) {
                    if (confirm('Are you sure to delete?')) {
                        axios.delete('http://localhost:8000/api/company/' + id)
                            .then(res => {
                                let company = res.data
                                this.companies = this.companies.filter(company => {
                                    return company.id != company.id;
                                    
                                })
                            });
                    }
                },

                fileChange(event) {
                    const file = event.target.files[0];
                    if (!file.type.includes("image")) {
                        this.url = null;
                    } else {
                        this.url = URL.createObjectURL(file);
                    }
                },
            }
        }
    </script>
</body>

</html>
