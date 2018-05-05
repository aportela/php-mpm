const mixinDateTime = {
    filters: {
        jsonDate2Human(jsonDate) {
            return (moment(jsonDate, "YYYY-MM-DDTHH:mm:ss.SZ").fromNow());
        }
    }
};
