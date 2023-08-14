// Date Picker

const checkInDateInput = document.getElementById("checkInDate");
const checkOutDateInput = document.getElementById("checkOutDate");

flatpickr(checkInDateInput, {
  minDate: "today",
  dateFormat: "Y-m-d",
});

flatpickr(checkOutDateInput, {
  minDate: "today",
  dateFormat: "Y-m-d",
});
