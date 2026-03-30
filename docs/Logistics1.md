# Logistics1 Module Documentation

## Overview

The Logistics1 module handles procurement, warehouse management, and fleet maintenance operations. It serves as the primary interface for managing medical supplies, equipment inventory, and vehicle fleet maintenance within the healthcare system. The module integrates with Core2 (inventory) and Logistics2 (procurement fulfillment) to ensure seamless supply chain operations.

## Authorization

All Logistics1 controllers enforce strict authorization:
- Only authenticated users with `role_slug = 'admin_logistics1'` can access Logistics1 functionalities
- Unauthorized access results in a 403 Forbidden response

## Procurement Management

### Controller: AdminLogistics1ProcurementController

Manages medical supply procurement and restocking requests.

#### Features

##### Inventory Monitoring
- Displays items requiring restock (Low Stock, Critical, Out of Stock)
- Search functionality by drug name or number
- Paginated inventory view

##### Procurement Requests
- Submit restock requests to Logistics2
- Supplier selection and quantity specification
- Employee tracking for request attribution

##### Procurement Logs
- View procurement history with requester and deliverer details
- Status tracking of procurement requests
- Employee name resolution for better readability

#### Database Tables
- `drug_inventory_core2`: Medical inventory data
- `procurement_log_logistics2`: Procurement request logs
- `employees`: Employee information for requesters/deliverers

## Warehouse Management

### Controller: AdminLogistics1WarehouseController

Provides warehouse inventory oversight and monitoring.

#### Features

##### Inventory Overview
- Complete warehouse inventory listing
- Search by drug name or number
- Paginated display for large inventories

##### Inventory Status Tracking
- Real-time stock level monitoring
- Status indicators (In Stock, Low Stock, etc.)
- Creation date ordering for recent items

#### Database Tables
- `drug_inventory_core2`: Warehouse inventory records

## Maintenance Management

### Controller: AdminMaintenanceController

Handles fleet maintenance operations and financial tracking.

#### Features

##### Maintenance Dashboard
- Vehicles currently under maintenance
- Recent repair logs from audit system
- Financial transaction history

##### Repair Recording
- Record maintenance activities on vehicles
- Cost tracking and financial ledger updates
- Automatic vehicle status updates to 'available'
- Employee attribution for maintenance actions

##### Financial Integration
- Maintenance cost recording in financials
- Payment status tracking
- Transaction date management

#### Database Tables
- `fleet_management_logistics2`: Vehicle fleet data
- `audit_logistics2`: Maintenance audit logs
- `maintenance_ledger_financials`: Financial transaction records
- `employees`: Employee information for performers

## Workflow Integration

### Module Dependencies
- **Core2**: Provides drug inventory data for monitoring and procurement
- **Logistics2**: Receives procurement requests and handles fulfillment
- **Financials**: Receives maintenance cost data for accounting

### Data Flow
1. **Procurement**: Logistics1 monitors inventory → Identifies low stock → Submits requests to Logistics2 → Receives fulfilled orders
2. **Warehouse**: Logistics1 views Core2 inventory → Monitors stock levels → Initiates procurement when needed
3. **Maintenance**: Logistics1 identifies maintenance needs → Records repairs → Updates vehicle status → Logs financial transactions

## Business Rules

### Procurement Rules
- Only items with critical stock levels trigger procurement alerts
- Requests must specify supplier and quantity
- Employee attribution required for audit trails

### Warehouse Rules
- Inventory searchable by multiple criteria
- Real-time status updates from Core2
- Pagination for performance with large inventories

### Maintenance Rules
- Repairs automatically set vehicle status to available
- All maintenance activities logged with costs
- Financial transactions created for each repair
- Employee identification required for accountability

## Security Considerations

- **Role-Based Access**: Strict enforcement of admin_logistics1 role
- **Data Validation**: Comprehensive input validation on all forms
- **Transaction Safety**: Database transactions for maintenance operations
- **Audit Trail**: Employee tracking on all procurement and maintenance actions

## Performance Optimizations

- **Efficient Queries**: Optimized joins for employee name resolution
- **Pagination**: Large datasets paginated appropriately
- **Search Optimization**: Database-level search with wildcards
- **AJAX Loading**: Potential for dynamic updates (not implemented in current controllers)

## Error Handling

- Graceful error messages for validation failures
- Employee identification checks before operations
- Transaction rollbacks for failed maintenance operations
- User-friendly feedback via session messages

## Integration Points

### External Systems
- **Core2 Inventory System**: Real-time inventory data
- **Logistics2 Procurement**: Request fulfillment
- **Financial Ledger**: Cost tracking and payments

### API Considerations
- RESTful endpoints for inventory queries
- Secure authentication for all operations
- JSON responses for AJAX integrations

## Future Enhancements

### Planned Features
- Automated procurement triggers based on stock levels
- Predictive maintenance scheduling
- Advanced inventory analytics
- Mobile app support for warehouse operations
- Integration with external suppliers

### Scalability Considerations
- Database indexing for search performance
- Caching strategies for frequently accessed inventory
- Modular architecture for feature expansion
- Background job processing for bulk operations

## Technical Implementation

### Controller Architecture
- Consistent authorization checks across all methods
- Database transaction usage for data integrity
- Employee resolution for user attribution
- Validation rules for data consistency

### Data Relationships
- Procurement logs link to inventory items
- Maintenance records connect to fleet vehicles
- Financial transactions reference audit logs
- Employee relationships for accountability

### Status Management
- Inventory status: In Stock, Low Stock, Critical, Out of Stock
- Procurement status: Pending, Approved, Delivered
- Vehicle status: Available, In Use, Maintenance
- Payment status: Paid, Unpaid

## Monitoring and Reporting

### Key Metrics
- Inventory turnover rates
- Procurement request fulfillment times
- Maintenance costs by vehicle
- Stock-out incidents

### Audit Capabilities
- Complete procurement history
- Maintenance activity logs
- Financial transaction trails
- Employee action tracking

## Compliance Considerations

### Healthcare Standards
- Medical supply chain compliance
- Equipment maintenance regulations
- Inventory tracking requirements
- Financial audit trails

### Data Security
- Sensitive inventory information protection
- Employee data privacy
- Financial transaction security
- Access control enforcement
