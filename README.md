# 🚗 Supervised Driving Experience Log

A complete PHP + MySQL web application for tracking and analyzing supervised driving experiences. Built with vanilla PHP (no frameworks), responsive design, and interactive statistics.

## 📋 Technical Description

This application is a full-featured driving log management system that demonstrates modern web development practices using only core technologies. The application enables learner drivers to track their supervised driving sessions with comprehensive data including weather conditions, traffic levels, supervisors, and road types.

### Original Features

1. **Many-to-Many Relationship Implementation**: The app implements a proper many-to-many relationship between driving experiences and road types through a junction table (`experience_road_type`), allowing each drive to be associated with multiple road types (e.g., Highway + Residential + Parking Lot).

2. **Object-Oriented Programming (OOP)**: Full OOP implementation with classes for DrivingExperience, Weather, Traffic, Supervisor, and RoadType. Each class includes properties, methods for CRUD operations, validation, and data management.

3. **ID Anonymization & Session Security**: All database IDs are encoded/encrypted when passed in URLs and stored in sessions, preventing direct exposure of primary keys. Uses SHA-256 hashing with secret keys for enhanced security.

4. **Complete CRUD Operations**: 
   - **Create**: Add new driving experiences
   - **Read**: View summary tables and statistics
   - **Update**: Edit existing experiences with pre-populated forms
   - **Delete**: Remove experiences with confirmation page

5. **Mobile-First Responsive Design**: The entry form uses CSS Grid and Flexbox with media queries to provide an optimized mobile experience with larger touch targets, while the summary tables are desktop-optimized with horizontal scrolling support.

6. **ChartJS Data Visualization**: Four interactive charts display statistics:
   - Doughnut/gauge chart for total km progress with visual goal tracking
   - Bar chart for total kilometers by weather condition
   - Horizontal bar chart for drive counts by road type
   - Line chart showing distance trends over months

7. **Real-time Form Defaults**: The add drive form automatically populates with the current date/time using HTML5 `datetime-local` input, providing a streamlined user experience.

8. **Date Range Filtering**: Summary and statistics pages support optional date range filtering, allowing users to analyze specific time periods.

9. **Dynamic Variable Management**: Users can add custom weather conditions, traffic levels, road types, and supervisors without modifying the database directly.

10. **Security-First Architecture**: All database queries use PDO prepared statements, output is escaped with `htmlspecialchars()`, input validation prevents invalid data submission, and transactions ensure data integrity.

11. **Session-Based Flash Messages**: Success and error messages persist across page redirects using PHP sessions, providing clear user feedback for all operations.

12. **Aggregate Statistics Dashboard**: The home page displays real-time statistics including total drives, total kilometers, and average distance per drive.

13. **Semantic HTML5 Structure**: Uses proper semantic elements (`<header>`, `<nav>`, `<main>`, `<section>`, `<footer>`) for better accessibility and SEO.

14. **Enhanced User Experience**: Edit and delete actions with clear visual feedback, confirmation dialogs, and intuitive action buttons in the summary table.

## 🗂️ Project Structure

```
PhpFinalProject/
├── config/
│   └── db.php                 # Database connection configuration
├── database/
│   ├── schema.sql            # Database schema with ERD explanation
│   └── seed.sql              # Sample data for testing
├── includes/
│   ├── header.php            # Common header with navigation
│   ├── footer.php            # Common footer
│   ├── classes.php           # OOP classes (NEW)
│   └── functions.php         # Reusable PHP functions library
├── public/
│   ├── css/
│   │   └── style.css         # Handwritten responsive CSS
│   ├── index.php             # Home page / Dashboard
│   ├── add_drive.php         # Add new driving experience form
│   ├── edit_drive.php        # Edit existing experience (NEW)
│   ├── delete_drive.php      # Delete with confirmation (NEW)
│   ├── summary.php           # Summary table with total km
│   ├── stats.php             # Statistics with ChartJS visualizations
│   └── manage_variables.php  # Add/manage custom variables
└── README.md                 # This file
```

## 🗄️ Database Schema

### Tables

1. **driving_experience** - Main table storing each driving session
   - `id` (PK)
   - `drive_datetime` (DATETIME)
   - `km` (DECIMAL 6,2)
   - `notes` (TEXT)
   - Foreign keys: `weather_id`, `traffic_id`, `supervisor_id`

2. **weather** - Lookup table for weather conditions

3. **traffic** - Lookup table for traffic conditions

4. **supervisor** - Table storing supervisor information

5. **road_type** - Lookup table for road types

6. **experience_road_type** - Junction table for many-to-many relationship
   - Composite PK: (`experience_id`, `road_type_id`)

### Entity Relationship Diagram (ERD)

```
┌─────────────────────┐
│      weather        │
│  id (PK)           │◄─────┐
│  label             │      │
└─────────────────────┘      │
                             │ many-to-one
┌─────────────────────┐      │
│      traffic        │      │
│  id (PK)           │◄─────┤
│  label             │      │
└─────────────────────┘      │
                             │
┌─────────────────────┐      │
│    supervisor       │      │
│  id (PK)           │◄─────┤
│  name              │      │
└─────────────────────┘      │
                             │
┌─────────────────────────────────┐
│   driving_experience            │
│  id (PK)                       │
│  drive_datetime                │
│  km                            │
│  notes                         │
│  weather_id (FK)    ───────────┘
│  traffic_id (FK)    
│  supervisor_id (FK) 
└──────────┬──────────────────────┘
           │ many-to-many
           │
┌──────────┴──────────────┐
│ experience_road_type    │
│  experience_id (PK, FK)│
│  road_type_id (PK, FK) │◄────┐
└─────────────────────────┘     │
                                │
┌─────────────────────┐         │
│     road_type       │         │
│  id (PK)           │─────────┘
│  label             │
└─────────────────────┘
```

## 🚀 Installation & Deployment

### Local Development Setup

1. **Install Prerequisites**
   - PHP 7.4 or higher
   - MySQL 5.7 or higher / MariaDB 10.2 or higher
   - Web server (Apache, Nginx, or PHP built-in server)

2. **Clone/Download Project**
   ```bash
   # Download or extract the project files to your development directory
   cd d:\Projects\PhpFinalProject
   ```

3. **Create Database**
   ```sql
   CREATE DATABASE driving_experience CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. **Import Schema and Seed Data**
   ```bash
   mysql -u root -p driving_experience < database/schema.sql
   mysql -u root -p driving_experience < database/seed.sql
   ```

5. **Configure Database Connection**
   
   Edit [config/db.php](config/db.php):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'driving_experience');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Your MySQL password
   ```

6. **Start Development Server**
   ```bash
   cd public
   php -S localhost:8000
   ```

7. **Access Application**
   
   Open browser: `http://localhost:8000`

### Deployment to Alwaysdata (or similar hosting)

#### Step 1: Prepare Hosting Environment

1. **Create MySQL Database**
   - Log into Alwaysdata admin panel
   - Go to Databases → MySQL
   - Create a new database (e.g., `username_driving`)
   - Note: database name, username, password, and host

2. **Access SSH or phpMyAdmin**
   - Use phpMyAdmin or SSH access to import SQL files

#### Step 2: Import Database

1. **Import Schema**
   - In phpMyAdmin, select your database
   - Go to "Import" tab
   - Upload `database/schema.sql`
   - Click "Go"

2. **Import Seed Data**
   - Repeat process with `database/seed.sql`

#### Step 3: Configure Application

1. **Update Database Configuration**
   
   Edit [config/db.php](config/db.php) with your hosting credentials:
   ```php
   define('DB_HOST', 'mysql-username.alwaysdata.net'); // Your MySQL host
   define('DB_NAME', 'username_driving');              // Your database name
   define('DB_USER', 'username_dbuser');               // Your database user
   define('DB_PASS', 'your_secure_password');          // Your database password
   ```

#### Step 4: Upload Files

1. **Upload via FTP/SFTP**
   - Connect using FileZilla or similar FTP client
   - Host: `ftp-username.alwaysdata.net`
   - Upload entire project structure to your web directory (e.g., `/www/`)
   
2. **File Structure on Server**
   ```
   /www/
   ├── config/
   ├── database/
   ├── includes/
   └── public/        # This should be your web root
   ```

3. **Set Web Root**
   - In Alwaysdata admin: Sites → Edit site
   - Set "Root directory" to: `/www/public`
   - This ensures only the public folder is accessible via web

#### Step 5: Set Permissions

```bash
# If using SSH, set appropriate permissions
chmod 755 /www/public
chmod 644 /www/public/*.php
chmod 644 /www/config/db.php
```

#### Step 6: Test Deployment

1. Visit your domain: `https://yourusername.alwaysdata.net`
2. You should see the home page with sample data
3. Test adding a new driving experience
4. Verify statistics charts load correctly

### Environment-Specific Configuration

For different environments, you can modify [config/db.php](config/db.php):

```php
// Example: Environment detection
$environment = getenv('APP_ENV') ?: 'production';

if ($environment === 'development') {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'driving_experience');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // Production settings
    define('DB_HOST', 'mysql-username.alwaysdata.net');
    define('DB_NAME', 'username_driving');
    define('DB_USER', 'username_dbuser');
    define('DB_PASS', 'your_secure_password');
}
```

## 🔒 Security Features

1. **PDO Prepared Statements**: All database queries use parameterized statements preventing SQL injection
2. **Output Escaping**: All user-generated content is escaped with `htmlspecialchars()`
3. **Input Validation**: Server-side validation for all form submissions
4. **Session Security**: Session-based messaging for secure cross-page communication
5. **Error Handling**: Database errors are logged and user-friendly messages are displayed
6. **CSRF Protection**: Forms can be enhanced with CSRF tokens if needed

## 📱 Responsive Design

- **Mobile (< 768px)**: Single-column layout, optimized form inputs, stacked cards
- **Tablet (768px - 1024px)**: Two-column grid, improved spacing
- **Desktop (> 1024px)**: Multi-column layouts, full-width tables, enhanced charts

## 📊 Features Overview

### Core Functionality

- ✅ **Add Driving Experience**: Mobile-optimized form with HTML5 inputs
- ✅ **Edit Driving Experience**: Pre-populated form with all existing data
- ✅ **Delete Driving Experience**: Confirmation page with experience details
- ✅ **View Summary**: Sortable table with date range filtering and action buttons
- ✅ **Statistics Dashboard**: ChartJS visualizations with 4 different charts
- ✅ **Manage Variables**: Add custom options dynamically
- ✅ **Total KM Tracking**: Real-time aggregate calculations
- ✅ **ID Anonymization**: Encrypted IDs in URLs and sessions

### Many-to-Many Implementation

The app demonstrates a proper many-to-many relationship:
- One drive can have multiple road types
- One road type can be associated with multiple drives
- Junction table handles the relationship with composite primary key

### Charts & Visualizations

1. **Total KM Gauge**: Doughnut chart showing progress toward goal (500 km)
2. **KM by Weather**: Bar chart showing total distance per weather condition
3. **Drives by Road Type**: Horizontal bar chart of usage frequency
4. **Monthly Trends**: Line chart showing distance over time

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+ (no frameworks) with OOP
- **Database**: MySQL 5.7+ / MariaDB 10.2+
- **Frontend**: HTML5, CSS3, JavaScript (ES6)
- **Charts**: Chart.js 4.4.0 (CDN)
- **Database Access**: PDO with prepared statements
- **Design**: CSS Grid, Flexbox, Mobile-first responsive
- **Security**: ID encryption, session management, input validation

## 📝 Usage Guide

### Adding a Driving Experience

1. Click "Add New Drive" from home page
2. Fill in all required fields:
   - Date & Time (defaults to current)
   - Distance in kilometers
   - Weather condition
   - Traffic level
   - Supervisor
   - Road types (select multiple)
   - Optional notes
3. Click "Save Driving Experience"
4. Redirected to summary page with success message
### Editing a Driving Experience

1. Navigate to "View Summary"
2. Click the ✏️ edit button next to any experience
3. Form pre-populates with existing data
4. Modify any fields as needed
5. Click "Update Driving Experience"
6. Changes saved and redirected to summary

### Deleting a Driving Experience

1. Navigate to "View Summary"
2. Click the 🗑️ delete button next to any experience
3. Confirmation page shows experience details
4. Click "Yes, Delete This Experience" to confirm
5. Or click "Cancel, Keep This Experience" to abort
6. Redirected to summary with appropriate message
### Viewing Summary

1. Navigate to "View Summary"
2. See all experiences in table format
3. View total kilometers at top
4. Optional: Filter by date range
5. Road types displayed as comma-separated list

### Analyzing Statistics

1. Go to "Statistics" page
2. View three interactive ChartJS charts
3. See summary statistics cards
4. Charts update based on all data

### Managing Variables

1. Click "Manage Options"
2. View existing options for each category
3. Add new weather conditions, traffic types, road types, or supervisors
4. New options immediately available in add form

## 🧪 Testing Checklist

- [x] Database connection successful
- [x] Add driving experience saves correctly
- [x] Edit driving experience updates correctly
- [x] Delete driving experience removes record
- [x] Many-to-many road types save properly
- [x] Summary table displays all data with actions
- [x] Total KM calculates accurately
- [x] Date filtering works on summary page
- [x] All four charts render on stats page
- [x] Manage variables adds new options
- [x] Mobile responsive (test on phone/narrow window)
- [x] Form validation shows errors
- [x] Success/error messages display after actions
- [x] IDs are anonymized in URLs
- [x] OOP classes function correctly
- [x] Transactions handle multi-table operations

## 🐛 Troubleshooting

### Database Connection Failed

- Verify MySQL service is running
- Check credentials in [config/db.php](config/db.php)
- Ensure database exists and schema is imported
- Check if PHP PDO MySQL extension is installed: `php -m | grep pdo_mysql`

### Charts Not Displaying

- Check browser console for JavaScript errors
- Verify Chart.js CDN is accessible
- Ensure `$use_chartjs = true` is set in stats.php
- Clear browser cache

### Page Not Found / 404 Errors

- Ensure web root is set to `/public` folder
- Check `.htaccess` if using Apache
- Verify file paths in includes use correct relative paths

### Form Submission Not Working

- Check PHP error logs: `tail -f /var/log/apache2/error.log`
- Verify form action points to correct file
- Ensure POST data is being received
- Check database foreign key constraints

## 📄 License

This project is created for educational purposes as part of a PHP final project.

## 👨‍💻 Development

### Code Structure

- **config/**: Database configuration
- **database/**: SQL schema and seed files
- **includes/**: Reusable PHP components
- **public/**: Web-accessible files (application entry point)

### Best Practices Implemented

1. **Separation of Concerns**: Logic separated into classes, functions, includes, and pages
2. **DRY Principle**: Reusable functions and OOP classes
3. **Security First**: Prepared statements, input validation, output escaping, ID anonymization
4. **Responsive Design**: Mobile-first CSS with media queries
5. **Semantic HTML**: Proper use of HTML5 semantic elements
6. **Accessibility**: Form labels, ARIA attributes where needed
7. **Performance**: Single database connection, efficient queries, transactions
8. **OOP Design**: Classes for all entities with proper encapsulation
9. **Session Security**: Encoded IDs, flash messages, data protection

## 🎯 Future Enhancements

- User authentication and multi-user support
- DataTable.js for advanced table sorting/filtering
- Export to PDF/CSV functionality
- More advanced filtering and search
- Photo upload for driving experiences
- GPS route tracking integration
- Progress tracking toward license requirements
- Email notifications for milestones
- Full MVC architecture refactoring

## 📞 Support

For issues or questions:
1. Check the troubleshooting section above
2. Review PHP error logs
3. Verify database connection and data
4. Check browser console for JavaScript errors

---

**Built with ❤️ using vanilla PHP, MySQL, and modern web standards.**
