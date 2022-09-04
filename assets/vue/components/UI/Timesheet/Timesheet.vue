<template>
  <div class="timeWrapper" v-if="window_is_open">
    <div class="item timesheet" id="worktime">
      <div class="close" v-on:click="closeWindow">
        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M18 1L1 18" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M1 1L18 18" stroke="#283848" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="label">
        Время работы
      </div>
      <div class="timeArea">
        <div class="days">
          <div class="empty"></div>
          <div class="day" v-for="day in days">
            {{ day.day }}
          </div>
        </div>
        <div class="list">
          <div class="item" v-for="hour in hours" :key="hour.id">
            <div class="time">{{ hour.hour }}</div>
            <div v-for="n in days.length" class="checkbox-parent">
              <input class="timeCheck" type="checkbox" v-model="checkedValues" :value="{day: n, hour: hour.id-1}"
                     v-bind:data-hour="'hour-'+hour.id" v-bind:data-day="'day-'+n">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {MUTATION_SET_FILTER_WORK_TIME} from "../../Catalog/Search/store/types/mutations";
import {ACTION_SEARCH_REQUEST} from "@/components/Catalog/Search/store/types/actions";


export default {
  name: 'Timesheet',
  data() {
    return {
      window_is_open: false,
      checkedValues: [],
      days: [
        {
          day: 'Пн'
        },
        {
          day: 'Вт'
        },
        {
          day: 'Ср'
        },
        {
          day: 'Чт'
        },
        {
          day: 'Пт'
        },
        {
          day: 'Сб'
        },
        {
          day: 'Вс'
        }
      ],
      hours: [
        {
          id: 1,
          hour: '00:00'
        },
        {
          id: 2,
          hour: '01:00'
        },
        {
          id: 3,
          hour: '02:00'
        },
        {
          id: 4,
          hour: '03:00'
        },
        {
          id: 5,
          hour: '04:00'
        },
        {
          id: 6,
          hour: '05:00'
        },
        {
          id: 7,
          hour: '06:00'
        },
        {
          id: 8,
          hour: '07:00'
        },
        {
          id: 9,
          hour: '08:00'
        },
        {
          id: 10,
          hour: '09:00'
        },
        {
          id: 11,
          hour: '10:00'
        },
        {
          id: 12,
          hour: '11:00'
        },
        {
          id: 13,
          hour: '12:00'
        },
        {
          id: 14,
          hour: '13:00'
        },
        {
          id: 15,
          hour: '14:00'
        },
        {
          id: 16,
          hour: '15:00'
        },
        {
          id: 17,
          hour: '16:00'
        },
        {
          id: 18,
          hour: '17:00'
        },
        {
          id: 19,
          hour: '18:00'
        },
        {
          id: 20,
          hour: '19:00'
        },
        {
          id: 21,
          hour: '20:00'
        },
        {
          id: 22,
          hour: '21:00'
        },
        {
          id: 23,
          hour: '22:00'
        },
        {
          id: 24,
          hour: '23:00'
        },
      ],
      work_time_data: []
    }
  },
  methods: {
    collectWorkHours() {
      let component = this;
      this.work_time_data = []
      this.checkedValues.forEach((value) => {
        let day = value.day;
        let hour = value.hour;
        component.work_time_data.push({day: day, hour: hour});
      })
    },
    openWindow: function () {
      this.window_is_open = true;
    },
    closeWindow: function () {
      this.window_is_open = false;
      this.collectWorkHours();
      this.getTimesheet();
      this.$store.commit(MUTATION_SET_FILTER_WORK_TIME, this.work_time_data)
      this.$store.dispatch(ACTION_SEARCH_REQUEST)
    },
    getTimesheet: function () {
      this.$emit('getTimesheet', {
        time: this.work_time_data,
      })
    },
    resetCheckedValues: function () {
      this.checkedValues = [];
    }
  }
}
</script>

<style scoped lang="scss">
.timeWrapper {
  width: 100vw;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 9999;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;

  .timesheet {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    background: rgba(255, 255, 255);
    padding: 20px 40px;
    border-radius: 10px;
    position: relative;

    .close {
      position: absolute;
      top: 10px;
      right: 10px;
      cursor: pointer;
    }

    .label {
      margin-bottom: 14px;
      font-weight: 700;
    }

    .timeArea {
      background: #EDF4FE;
      border-radius: 10px;
      width: 548px;
      position: relative;
      padding-top: 40px;
      padding-right: 18px;

      .days {
        display: flex;
        align-items: center;
        position: absolute;
        right: 0;
        top: 10px;

        .empty {
          width: 100%;
        }

        .day {
          margin-right: 44px;
          width: 50px;
          font-style: normal;
          font-weight: 600;
          font-size: 14px;
          line-height: 17px;
        }
      }

      .list {
        height: 445px;
        overflow-y: auto;
        overflow-x: hidden;

        .item {
          padding: 5px 20px;
          display: flex;
          align-items: center;

          .time {
            min-width: 93px;
            font-style: normal;
            font-weight: 600;
            font-size: 14px;
            line-height: 17px;
          }

          .checkbox-parent {
            display: inline-block;
            position: relative;
            margin-right: 32px;
            cursor: pointer;
            font-size: 22px;
            -webkit-user-select: none;
            user-select: none;

            input {
              width: 22px;
              height: 22px;
              border: 2px solid #87b1ca;
              border-radius: 5px;
            }
          }
        }

        &::-webkit-scrollbar {
          width: 3px;
        }

        &::-webkit-scrollbar-track {
          background: #E0E0E0;
          width: 1px;
        }

        &::-webkit-scrollbar-thumb {
          background: #87B1CA;
          border-radius: 123px;
          width: 3px;
        }
      }
    }

    .allow {
      margin-top: 17px;
      display: flex;
      align-items: center;

      input {
        width: 23px;
        height: 23px;
        margin-right: 12px;
      }

      label {
        font-weight: 400;
        font-size: 14px;
        line-height: 16px;
        color: #283848;
      }
    }
  }
}
</style>