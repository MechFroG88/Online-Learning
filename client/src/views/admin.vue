<template>
  <div id="_admin">
    <nav>
      <h5>
        Navigate <i class="icon-location" style="margin-left: 1rem;"></i>
      </h5>
      <ul>
        <li><a href="#user">User</a></li>
        <li><a href="#event">Event</a></li>
        <li><a href="#period">Periods</a></li>
        <li><a href="#class">Class</a></li>
        <li><a href="#subject">Subjects</a></li>
        <li><a href="#day">Day</a></li>
      </ul>
    </nav>
    <div class="dashboard">
      <div id="user" class="large">
        <dt-table
        ref="user"
        :columns="columns.user"
        :tableData="userArr"
        navbar="username"
        title>
          <template slot="title">
            Users
          </template>
        </dt-table>
      </div>

      <div id="event">
        <dt-table
        :columns="columns.event"
        title>
          <template slot="title">
            Events
          </template>
        </dt-table>
      </div>

      <div id="period">
        <dt-table
        :columns="columns.period"
        title>
          <template slot="title">
            Periods
          </template>
        </dt-table>
      </div>

      <div id="class">
        <dt-table
        :columns="columns.classes"
        navbar="class name"
        title>
          <template slot="title">
            Class
          </template>
        </dt-table>
      </div>

      <div id="subject">
        <dt-table
        :columns="columns.subject"
        navbar="subject name"
        title>
          <template slot="title">
            Subjects
          </template>
        </dt-table>
      </div>

      <div id="day">
        <dt-table
        :columns="columns.day"
        title>
          <template slot="title">
            Period count (per day)
          </template>
        </dt-table>
      </div>
    </div>
  </div>
</template>

<script>
import columns from '@/api/tableColumns';
import { createUser, getUsers, editUser, deleteUser } from '@/api/users';
import { createEvent, getAllEvents, editEvent, deleteEvent } from '@/api/event';


import dtTable from '@/components/table';
export default {
  components: {
    dtTable
  },
  mounted() {
    getUsers().then((data) => {
      if (data.status == 200) {
        this.userArr = data.data;
        // this.$refs.user.is_loading = false;
      }
    })
  },
  data: () => ({
    columns,
    userArr: []
  })
}
</script>

<style lang="scss">

</style>