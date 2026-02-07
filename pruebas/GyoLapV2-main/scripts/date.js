export function today() {
    const now = new Date();

    return new Date(
        now.getFullYear(),
        now.getMonth(),
        now.getDate(),
        12
    );
}

export function addMonths(date, months) {
    const firstDayOfMonth = new Date(
        date.getFullYear(),
        date.getMonth() + months,
        1,
        date.getHours()
    );
    const lastDayOfMonth = getLastDayOfMonthDate(firstDayOfMonth);

    const dayOfMonth = Math.min(date.getDate(), lastDayOfMonth.getDate());

    return new Date(
        date.getFullYear(),
        date.getMonth() + months,
        dayOfMonth,
        date.getHours()
      );
}


export function addDays(date, days) {
    return new Date(
        date.getFullYear(),
        date.getMonth(),
        date.getDate() + days,
        date.getHours()
    );
}

export function subtractDays(date, days) {
    return addDays(date, -days);
}

export function isTheSameDay(dateA, dateB) {
    return dateA.getFullYear() === dateB.getFullYear() && dateA.getMonth() === dateB.getMonth() && dateA.getDate() === dateB.getDate();
}

export function generateWeekDays(date){
    const weekDays = [];
    const firstWeekDay = subtractDays(date, date.getDay());

    for(let i = 0; i <= 6; i +=1){
        const weekDay = addDays(firstWeekDay, i);
        weekDays.push(weekDay);
    }
    return weekDays;
}

function getLastDayOfMonthDate(date) {
    return new Date(
        date.getFullYear(),
        date.getMonth() + 1,
        0,
        12
    );
}