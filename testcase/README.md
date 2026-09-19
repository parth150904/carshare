# CarShare Test Suite

This `testcase` folder is designed to keep all automated system checks, unit tests, and API verifications neat and organized for the CarShare project.

## Included Test Cases:
1. **`test_db_connection.php`** 
   - Verifies that the application can successfully connect to the MySQL database via `config.php`.
   - Ensures the core tables (`car_ride`, `users`) exist in the schema to prevent fatal application crashes.

2. **`test_fare_estimator.php`**
   - Automatically pings the Gemini AI Fare Estimator API (`user/api_fare_estimator.php`).
   - Simulates a ride from Ahmedabad to Surat and verifies if a valid JSON price and reasoning string are returned.

## How to Run the Tests:
To execute all the test cases at once in a beautiful UI, simply open the following URL in your browser:
**[http://localhost/carshare/testcase/run_all_tests.php](http://localhost/carshare/testcase/run_all_tests.php)**

## Adding New Tests:
To add a new test, simply create a new PHP file (e.g., `test_login_auth.php`) in this folder and include it in `run_all_tests.php`.

