# TODO List for Driver-Only Ride Completion and Review Prompt

## Tasks
- [x] Modify `complete_booking.php` to allow only drivers to complete rides by checking `driver_id == user_id` instead of `commuter_id == user_id`.
- [x] Update `active_booking.php` to remove the "Complete Booking" button and associated JavaScript function `completeBooking()`, while keeping the polling logic and review modal for commuters.
- [ ] Test the driver completion flow in `driver_active_booking.html`.
- [ ] Verify that commuters see the review prompt after driver completion via polling in `active_booking.php`.
- [ ] Ensure reviews are saved correctly in the database.
