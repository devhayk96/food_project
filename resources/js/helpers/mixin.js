import moment from 'moment';

export default {
    methods: {
        getValueTime(data) {
            return !!data.is_asap
                ? 'ASAP'
                : (data.hasOwnProperty('requested_time') && data.requested_time
                    ? moment(data.requested_time).format('HH:mm')
                    : ''
                )
        },

        getNumberOfLines() {
            if(localStorage.authUser) {
                let userData = JSON.parse(localStorage.authUser);
                return userData.number_of_lines != '' ? userData.number_of_lines
                : undefined;
            }
        }
    }
};
