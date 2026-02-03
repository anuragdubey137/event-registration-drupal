# Event Registration Module for Drupal 10

## Overview
This custom Drupal 10 module allows users to register for events via a custom form, stores registrations in a database, and sends email notifications. It also provides an admin interface to manage registrations, filter by event/date, view total participants, and export registration data as CSV.

## Installation Steps
1. Place the module in your custom modules directory:


2. Enable the module using Drush or the Drupal admin UI:


3. Clear caches:


4. Make sure the database tables are created (run the provided SQL file if needed):


## Module URLs / Forms

| Feature | URL | Description |
|---------|-----|-------------|
| Event Configuration Form | /admin/config/event-registration | Admin can add events with registration start/end dates, event name, category, and event date. |
| Event Registration Form | /event/register | Users can register for events. Form is available only between registration start and end dates. |
| Admin Registration Listing | /admin/event-registrations | Admin can view all registrations, filter by event date and name, see total participants, and export CSV. |

## Database Tables

**event_registration_config**

| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Unique event ID |
| registration_start | DATETIME | Event registration start date |
| registration_end | DATETIME | Event registration end date |
| event_date | DATE | Date of the event |
| event_name | VARCHAR | Event name |
| category | VARCHAR | Event category (Online Workshop, Hackathon, etc.) |

**event_registration**

| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Unique registration ID |
| full_name | VARCHAR | User full name |
| email | VARCHAR | User email |
| college_name | VARCHAR | User college |
| department | VARCHAR | User department |
| event_id | INT (FK) | Reference to event_registration_config.id |
| created | DATETIME | Timestamp of registration |

## Validation & Logic
- **Duplicate Registrations:** Users cannot register for the same event on the same date using the same email.  
- **Email Validation:** Proper email format is enforced.  
- **Text Fields:** Special characters are not allowed.  
- **AJAX Callbacks:** Event Date dropdown filters events by selected category. Event Name dropdown filters events by selected date and category.  
- **User Feedback:** Friendly validation messages are displayed for all errors.  

## Email Notifications
- Drupal Mail API is used for sending emails.  
- **To User:** Confirmation email including Name, Event Date, Event Name, and Category.  
- **To Admin:** Notification email with the same details if admin notifications are enabled in the configuration.  

## Admin Features
- Registration Listing: View all registrations in a table.  
- Filters: Filter by Event Date, Filter by Event Name (based on selected date)  
- Total Participants: Displayed dynamically based on selected filters.  
- CSV Export: Export all registrations as a CSV file.  
- Permissions: Accessible only to users with the custom permission view event registrations.  

## Configuration
Admin can configure: Admin notification email address, Enable/disable admin notifications.  
All configuration uses Drupal Config API. No hard-coded values are used.  

## Technical Details
- Drupal 10.x compatible  
- PSR-4 autoloading  
- Dependency Injection used (no direct \Drupal::service() in business logic)  
- Coding standards followed: Drupal CS  

## SQL File
A .sql file is provided to create the required tables:  
- event_registration_config  
- event_registration  

## Notes
- Commit your changes frequently when developing the module.  
- Always clear caches after enabling the module or updating configuration.  
- AJAX callbacks ensure dynamic dropdown updates without page reloads.  

## Author
Anurag Dubey
anurag.23bai11104@vitbhopal.ac.in
github repo link :- [link]https://github.com/anuragdubey137/event-registration-drupal
