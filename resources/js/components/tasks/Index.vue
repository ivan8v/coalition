<template>
    <div>
        <div class="card">
            <div class="card-header">
                <template v-if="formMode === 'create'">
                    Add New Task
                </template>
                <template v-else>
                    Editing Task
                </template>
            </div>
            <div class="card-body">
                <div class="container">
                    <div class="row">
                        <div class="form-group col-3">
                            <label>Name
                                <input v-model="taskName" class="form-control" type="text">
                            </label>
                        </div>
                        <div class="form-group col-3">
                            <label>Priority
                                <input v-model="priority" class="form-control" type="number" min="0" max="100">
                            </label>
                        </div>
                        <div class="form-group col-3">
                            <label>Belong to Project
                                <select v-model=selectedProject class="form-control" name="projects" id="projects">
                                    <option value="">--Select Project--</option>
                                    <option :value="project.id" v-for="project in projects">{{project.name}}</option>
                                </select>
                            </label>
                        </div>
                        <div class="col-12">
                            <button v-if="formMode === 'create'" v-promise-btn class="btn btn-primary btn-block"
                                    @click="addTask">Add
                            </button>
                            <button v-else v-promise-btn class="btn btn-warning btn-block" @click="editTask">Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <label>Filter By Project
            <select v-model="projectFilter" class="form-control">
                <option value="">All</option>
                <option :value="project.id" v-for="project in projects">{{project.name}}</option>
            </select>
        </label>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Tasks</th>
                <th scope="col">Name</th>
                <th scope="col">Priority</th>
                <th scope="col">Project</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <draggable tag="tbody" @change="handleSort" v-model="tasks" group="people" @start="drag=true" @end="drag=false">
                <tr v-for="task in tasks" :key="task.id">
                    <td scope="row">{{task.id}}</td>
                    <td>{{task.name}}</td>
                    <td>{{task.priority}}</td>
                    <td>{{task.project_id}}</td>
                    <td>
                        <button @click="setEditionMode(task)" class="btn btn-xs btn-warning">Edit</button>
                        <button @click="deleteTask(task)" class="btn btn-xs btn-danger">Delete</button>
                    </td>
                </tr>
            </draggable>
        </table>
    </div>
</template>

<script>
    export default {
        name: 'tasks-index',
        data() {
            return {
                tasks: [],
                projects: [],
                taskName: '',
                priority: 0,
                editTaskid: 0,
                selectedProject: '',
                projectFilter: '',
                formMode: 'create',
                params: {
                    params: {}
                }
            }
        },
        mounted() {
            this.getProjects();
        },
        computed: {
            sortedTasks() {
                return _.sortBy(this.tasks, 'priority');
            }
        },
        watch: {
            projectFilter(newValue) {
                this.params.params.projectFilter = newValue;
                this.getTasks();
            }
        },
        methods: {
            getProjects() {
                axios.get('/api/projects')
                    .then(response => {
                        this.projects = response.data;
                        this.getTasks();
                    })

            },
            getTasks() {
                axios.get('/api/tasks', this.params)
                    .then(response => {
                        this.tasks = _.sortBy(response.data, 'priority');
                    }).catch(error => {
                    console.log(error)
                })

            },
            setEditionMode(task) {
                this.formMode = 'edit';
                this.taskName = task.name;
                this.priority = task.priority;
                this.selectedProject = task.project_id;
                this.editTaskid = task.id;
            },
            editTask() {
                let data = {
                    name: this.taskName,
                    priority: this.priority,
                    projectId: this.selectedProject
                };
                return axios.patch(`/api/tasks/${this.editTaskid}`, data)
                    .then(response => {
                        Toast.fire({
                            icon: 'success',
                            title: 'Task Edited'
                        });
                        this.cleanForm();
                    }).catch(error => {
                        this.cleanForm();
                        if (error.response.status === 422) {
                            let errors = error.response.data.errors;

                            Toast.fire({
                                icon: 'error',
                                title: this.parseValidationErrors(errors)[0],
                            });
                        }
                    })
            },
            addTask() {
                let data = {
                    name: this.taskName,
                    priority: this.priority,
                    projectId: this.selectedProject
                };
                return axios.post('/api/tasks', data)
                    .then(response => {
                        Toast.fire({
                            icon: 'success',
                            title: 'Task Added'
                        });
                        this.tasks.push(response.data);
                        this.cleanForm();
                    }).catch(error => {
                        this.cleanForm();
                        if (error.response.status === 422) {
                            let errors = error.response.data.errors;

                            Toast.fire({
                                icon: 'error',
                                title: this.parseValidationErrors(errors)[0],
                            });
                        }
                    })
            },
            parseValidationErrors(validationErrors) {
                let errors = Object.values(validationErrors);
                errors = errors.flat();
                return errors;
            },
            deleteTask(task) {
                Swal.fire({
                    text: `Delete task number ${task.id}?`,
                    type: 'warning',
                    confirmButtonText: 'Delete!',
                    showLoaderOnConfirm: true,
                    showCancelButton: true,
                    preConfirm: () => {
                        return axios.delete(`/api/tasks/${task.id}`)
                            .then(res => {
                                this.tasks.splice(this.tasks.indexOf(task));
                            })
                            .catch(error => {
                                Swal.showValidationMessage(error)
                            })
                    },
                })
            },
            cleanForm() {
                this.formMode = 'create';
                this.taskName = '';
                this.priority = 0;
                this.selectedProject = '';
            },
            handleSort(data) {
                let oldTask = this.tasks[data.moved.oldIndex];
                let newTask = this.tasks[data.moved.newIndex];
                let oldPriority = oldTask.priority;
                let newPriority = newTask.priority;
                oldTask.priority = newPriority;
                newTask.priority = oldPriority;
                this.updatePriority(oldTask.id, newPriority);
                this.updatePriority(newTask.id, oldPriority)
            },
            updatePriority(taskId, priority) {
                let data = {
                    priority: priority,
                };
                return axios.patch(`/api/tasks/${taskId}`, data)
                    .then(response => {
                        Toast.fire({
                            icon: 'success',
                            title: 'Task Edited'
                        });
                        this.cleanForm();
                    }).catch(error => {
                        this.cleanForm();
                        if (error.response.status === 422) {
                            let errors = error.response.data.errors;

                            Toast.fire({
                                icon: 'error',
                                title: this.parseValidationErrors(errors)[0],
                            });
                        }
                    })
            }
        },
    }
</script>
