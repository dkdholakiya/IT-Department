/**
 * GMIU IT & CE Department — Shared Academic Calendar Data
 * Defines Public Holidays, Reserved Holidays, and Department Events
 */
const calendarData = {
    // Public Holidays (Red / Crimson Indicator)
    publicHolidays: [
        {
            date: "2026-01-14",
            name: "Makar Sankranti",
            description: "Traditional festival of kites celebrating the transition of the sun."
        },
        {
            date: "2026-01-26",
            name: "Republic Day",
            description: "National holiday celebrating the adoption of the Constitution of India."
        },
        {
            date: "2026-03-04",
            name: "Holi (2nd Day – Dhuleti)",
            description: "Festival of colours celebrating the arrival of spring."
        },
        {
            date: "2026-03-21",
            name: "Ramjan-Eid (Eid-Ul-Fitra)",
            description: "Islamic holiday marking the end of the dawn-to-sunset fasting of Ramadan."
        },
        {
            date: "2026-03-26",
            name: "Ram Navmi",
            description: "Hindu festival celebrating the birth of Lord Rama."
        },
        {
            date: "2026-04-03",
            name: "Good Friday",
            description: "Christian holiday commemorating the crucifixion of Jesus Christ."
        },
        {
            date: "2026-04-14",
            name: "Dr. Baba Saheb Ambedkar's Birthday",
            description: "Commemoration of the birth of Dr. B. R. Ambedkar, father of the Indian Constitution."
        },
        {
            date: "2026-05-27",
            name: "Bakri-Eid (Eid-Ul-Adha)",
            description: "Islamic holiday honoring the willingness of Ibrahim to sacrifice his son."
        },
        {
            date: "2026-06-26",
            name: "Muharram (Ashoora)",
            description: "Day of remembrance in Islam."
        },
        {
            date: "2026-07-16",
            name: "Rathyatra",
            description: "Annual chariot festival of Lord Jagannath."
        },
        {
            date: "2026-08-15",
            name: "Independence Day / Parsi New Year (Pateti)",
            description: "Celebrating National Independence Day and Parsi New Year."
        },
        {
            date: "2026-08-26",
            name: "Eid-e-Meeladunnabi",
            description: "Prophet Mohammad's Birthday commemoration."
        },
        {
            date: "2026-08-28",
            name: "Rakshabandhan",
            description: "Traditional festival celebrating the bond between brothers and sisters."
        },
        {
            date: "2026-09-02",
            name: "Janmashtami (Shravan Vad–8)",
            description: "Janmashtami holidays - Day 1"
        },
        {
            date: "2026-09-03",
            name: "Janmashtami (Shravan Vad–8)",
            description: "Janmashtami holidays - Day 2"
        },
        {
            date: "2026-09-04",
            name: "Janmashtami (Shravan Vad–8)",
            description: "Janmashtami holidays - Day 3"
        },
        {
            date: "2026-09-05",
            name: "Janmashtami (Shravan Vad–8)",
            description: "Janmashtami holidays - Day 4"
        },
        {
            date: "2026-09-14",
            name: "Ganesh Chaturthi",
            description: "Hindu festival celebrating the birth of Lord Ganesha."
        },
        {
            date: "2026-10-02",
            name: "Mahatma Gandhi's Birthday",
            description: "National holiday commemorating the birth of Mahatma Gandhi."
        },
        {
            date: "2026-10-20",
            name: "Dusshera (Vijaya Dashmi)",
            description: "Festival marking the victory of good over evil."
        },
        {
            date: "2026-10-31",
            name: "Sardar Vallabhbhai Patel Jayanti",
            description: "Birth anniversary of Sardar Vallabhbhai Patel, the Iron Man of India."
        },
        {
            date: "2026-11-07",
            name: "Diwali Holidays",
            description: "Diwali holidays - Day 1"
        },
        {
            date: "2026-11-08",
            name: "Diwali Holidays (Diwali)",
            description: "Diwali holidays - Day 2 (Festival of Lights)"
        },
        {
            date: "2026-11-09",
            name: "Diwali Holidays (New Year)",
            description: "Diwali holidays - Day 3 (Vikram Samvat Gujarati New Year)"
        },
        {
            date: "2026-11-10",
            name: "Diwali Holidays (Bhai Dooj)",
            description: "Diwali holidays - Day 4 (Bhai Beej / Bhai Dooj)"
        },
        {
            date: "2026-11-11",
            name: "Diwali Holidays",
            description: "Diwali holidays - Day 5"
        },
        {
            date: "2026-11-12",
            name: "Diwali Holidays",
            description: "Diwali holidays - Day 6"
        },
        {
            date: "2026-11-13",
            name: "Diwali Holidays",
            description: "Diwali holidays - Day 7"
        },
        {
            date: "2026-11-14",
            name: "Diwali Holidays",
            description: "Diwali holidays - Day 8"
        },
        {
            date: "2026-11-15",
            name: "Diwali Holidays",
            description: "Diwali holidays - Day 9"
        },
        {
            date: "2026-11-16",
            name: "Diwali Holidays",
            description: "Diwali holidays - Day 10"
        },
        {
            date: "2026-12-25",
            name: "Christmas",
            description: "Annual festival commemorating the birth of Jesus Christ."
        }
    ],

    // Reserved Holidays (Purple / Violet Indicator)
    reservedHolidays: [
        {
            date: "2026-01-01",
            name: "Christian New Year Day",
            description: "Optional holiday observing the first day of the Gregorian calendar."
        },
        {
            date: "2026-01-15",
            name: "Vassi Uttarayan (Next Day to Makar Sankranti)",
            description: "Optional holiday observing the day following Makar Sankranti."
        },
        {
            date: "2026-01-31",
            name: "Vishvakarma Jayanti",
            description: "Optional holiday celebrating Vishvakarma, the divine architect."
        },
        {
            date: "2026-02-04",
            name: "Shab-e-Barat",
            description: "Optional holiday observing the night of forgiveness in Islamic tradition."
        },
        {
            date: "2026-02-04",
            name: "Birthday of Dhani Matang Dev",
            description: "Optional holiday celebrating the birth anniversary of Dhani Matang Dev."
        },
        {
            date: "2026-03-03",
            name: "Holi",
            description: "Optional holiday observing the first day of Holi."
        },
        {
            date: "2026-03-11",
            name: "Shahadat-e-Hazarat Ali",
            description: "Optional holiday observing the martyrdom of Hazarat Ali."
        },
        {
            date: "2026-03-19",
            name: "Gudi Padvo",
            description: "Optional holiday celebrating the traditional New Year for Marathi and Konkani Hindus."
        },
        {
            date: "2026-03-21",
            name: "Jamshedi Navroz (Parsi)",
            description: "Optional holiday celebrating the Spring Equinox / Parsi New Year."
        },
        {
            date: "2026-04-01",
            name: "Hatkeshvar Jayanti",
            description: "Optional holiday celebrating Hatkeshvar Jayanti."
        },
        {
            date: "2026-04-02",
            name: "Pesah (1st Day) (Yahudi)",
            description: "Optional holiday observing Passover."
        },
        {
            date: "2026-04-02",
            name: "Hanuman Jayanti",
            description: "Optional holiday celebrating the birth of Lord Hanuman."
        },
        {
            date: "2026-04-13",
            name: "Maha Prabhuji's Praktyotsava",
            description: "Optional holiday celebrating the birth anniversary of Vallabhacharya."
        },
        {
            date: "2026-04-21",
            name: "Shree Adhya Jagadguru Shankaracharya Jayanti",
            description: "Optional holiday commemorating the birth of Adi Shankara."
        },
        {
            date: "2026-04-22",
            name: "Zarthost-no-Disho (Parsi Kadmi)",
            description: "Optional holiday observing the death anniversary of Prophet Zoroaster (Kadmi calendar)."
        },
        {
            date: "2026-05-01",
            name: "Buddha Purnima",
            description: "Optional holiday celebrating the birth, enlightenment, and death of Gautama Buddha."
        },
        {
            date: "2026-05-22",
            name: "Shavuoth (Yahudi)",
            description: "Optional holiday observing Shavuot."
        },
        {
            date: "2026-05-22",
            name: "Zarthost-no-Disho (Parsi Shahenshahi)",
            description: "Optional holiday observing the death anniversary of Prophet Zoroaster (Shahenshahi calendar)."
        },
        {
            date: "2026-06-18",
            name: "Guru Arjundev's Martyrdom Day",
            description: "Optional holiday commemorating the martyrdom of Guru Arjan Dev."
        },
        {
            date: "2026-06-25",
            name: "9th Muharram",
            description: "Optional holiday observing the eve of Ashoora."
        },
        {
            date: "2026-07-13",
            name: "Gatha Gahamber (Parsi Kadmi)",
            description: "Optional holiday celebrating Gatha Gahamber (Kadmi calendar)."
        },
        {
            date: "2026-07-15",
            name: "Parsi New Year Day Eve (Parsi Kadmi)",
            description: "Optional holiday observing the eve of Parsi New Year (Kadmi calendar)."
        },
        {
            date: "2026-07-16",
            name: "Rathayatra",
            description: "Optional holiday observing the Chariot Festival."
        },
        {
            date: "2026-07-16",
            name: "Parsi New Year Day (Parsi Kadmi)",
            description: "Optional holiday celebrating Parsi New Year (Kadmi calendar)."
        },
        {
            date: "2026-07-21",
            name: "Khordad Sal (Parsi Kadmi)",
            description: "Optional holiday observing the birth anniversary of Prophet Zoroaster (Kadmi calendar)."
        },
        {
            date: "2026-07-23",
            name: "Tisha-be-aav (Yahudi)",
            description: "Optional holiday observing Tisha B'Av."
        },
        {
            date: "2026-08-12",
            name: "Gatha Gahamber (Parsi Shahenshahi)",
            description: "Optional holiday celebrating Gatha Gahamber (Shahenshahi calendar)."
        },
        {
            date: "2026-08-12",
            name: "Shahadat-e-Imam Hasan",
            description: "Optional holiday observing the martyrdom of Imam Hasan."
        },
        {
            date: "2026-08-14",
            name: "Parsi New Year Day Eve (Parsi Shahenshahi)",
            description: "Optional holiday observing the eve of Parsi New Year (Shahenshahi calendar)."
        },
        {
            date: "2026-08-20",
            name: "Khordad Sal (Parsi Shahenshahi)",
            description: "Optional holiday observing the birth anniversary of Prophet Zoroaster (Shahenshahi calendar)."
        },
        {
            date: "2026-08-26",
            name: "Onam",
            description: "Optional holiday celebrating the harvest festival of Kerala."
        },
        {
            date: "2026-08-31",
            name: "Id-e-Maulud",
            description: "Optional holiday commemorating the birth of Prophet Muhammad."
        },
        {
            date: "2026-09-05",
            name: "Nand Utsav",
            description: "Optional holiday celebrating the day after Janmashtami."
        },
        {
            date: "2026-09-08",
            name: "Paryusan (1st Day)",
            description: "Optional holiday observing the commencement of Paryushana festival."
        },
        {
            date: "2026-09-09",
            name: "Paryusan (2nd Day)",
            description: "Optional holiday observing the second day of Paryushana."
        },
        {
            date: "2026-09-14",
            name: "Ganesh Chaturthi",
            description: "Optional holiday celebrating Ganesh Chaturthi."
        },
        {
            date: "2026-09-16",
            name: "Samvatsari",
            description: "Optional holiday observing Samvatsari, the festival of forgiveness."
        },
        {
            date: "2026-09-21",
            name: "Yom Kippur (Yahudi)",
            description: "Optional holiday observing Yom Kippur."
        },
        {
            date: "2026-11-07",
            name: "Dhanteras",
            description: "Optional holiday celebrating Dhanteras."
        },
        {
            date: "2026-11-24",
            name: "Dev Diwali",
            description: "Optional holiday celebrating Dev Deepawali."
        }
    ],

    // Custom Academic/Departmental Events (Cyan / Gold Indicator)
    customEvents: [],

    // Recurring Weekly Events
    recurringEvents: [
        {
            name: "Departmental Meeting",
            dayOfWeek: 5, // Friday
            time: "09:30 AM to 10:30 AM",
            startDate: "2026-07-03",
            category: "meeting",
            department: ["Information Technology", "Computer Engineering"],
            description: "Weekly departmental meeting with all IT & CE faculty members."
        },
        {
            name: "Departmental Meeting",
            dayOfWeek: 6, // Saturday
            time: "05:00 PM to 06:00 PM",
            startDate: "2026-07-11",
            category: "meeting",
            department: ["Information Technology", "Computer Engineering"],
            description: "Weekly departmental review meeting for IT & CE faculty."
        }
    ]
};
