# Logistics2 Module Documentation

## Overview

The Logistics2 module manages the operational logistics including fleet management, vendor relations, procurement fulfillment, and document tracking. It serves as the execution arm of the logistics system, handling vehicle assignments, shipment tracking, vendor management, and audit logging. The module integrates with Logistics1 (procurement requests), Core1 (medical documents), and Core2 (inventory) to ensure seamless supply chain operations.

## Authorization

All Logistics2 controllers enforce strict authorization:
- Only authenticated users with `role_slug = 'admin_logistics2'` can access Logistics2 functionalities
- Unauthorized access results in a 403 Forbidden response

## Fleet Management

### Controller: AdminFleetController

Manages the vehicle fleet for transportation and delivery operations.

#### Features

##### Fleet Overview
- Complete vehicle listing with status tracking
- Vehicle types and model information
- Real-time availability status

##### Vehicle Addition
- Add new vehicles to the fleet
- Unique plate number validation
- Automatic status assignment as 'available'

##### Status Management
- Manual status updates (available/maintenance)
- Status tracking for operational planning

#### Database Tables
- `fleet_management_logistics2`: Vehicle records with status, type, and maintenance info

## Vendor Management

### Controller: AdminVendorController

Handles procurement request processing and vendor coordination.

#### Features

##### Request Processing
- Receive and process pending requests from Logistics1
- Vehicle assignment for procurement fulfillment
- Status updates across procurement pipeline

##### Vendor Coordination
- Supplier information preservation
- Delivery address management
- Quantity and item tracking

##### Fleet Integration
- Available vehicle selection for assignments
- Automatic vehicle status updates to 'in_use'

#### Database Tables
- `purchase_orders_logistics1`: Procurement requests from Logistics1
- `vendor_logistics2`: Vendor processing records
- `vehicle_reservations`: Vehicle assignments
- `fleet_management_logistics2`: Vehicle status updates

### Controller: AdminVendorPortalController

Manages vendor relationships and accreditation.

#### Features

##### Vendor CRUD Operations
- Create, read, update, delete vendor records
- Automatic vendor code generation (VEN-XXXX format)
- Soft delete functionality

##### Vendor Information
- Contact details, accreditation dates
- Category classification and tax information
- Business permit and compliance tracking

##### Accreditation Management
- Accreditation date and expiry tracking
- Status management (active/inactive)
- Notes and additional vendor information

#### Database Tables
- `vendor_portal_logistics2`: Vendor master data with accreditation details

## Vehicle Reservations and Shipments

### Controller: AdminVehicleReservationController

Manages vehicle reservations and shipment tracking.

#### Features

##### Reservation Management
- View all vehicle reservations with status
- Integration with vendor logistics and purchase orders
- Delivery status tracking

##### Shipment Dispatch
- Start transit with cost recording
- Update delivery status to 'in_transit'
- Cascade status updates across related tables

##### Delivery Completion
- Mark deliveries as completed
- Update vehicle availability
- Audit logging with cost and details
- Inventory status updates

#### Database Tables
- `vehicle_reservations`: Reservation and delivery tracking
- `vendor_logistics2`: Vendor processing status
- `purchase_orders_logistics1`: Purchase order status
- `fleet_management_logistics2`: Vehicle status
- `audit_logistics2`: Delivery audit logs

## Document Tracking

### Controller: AdminDocumentTrackingLabOrdersController

Provides document tracking for medical orders from Core1.

#### Features

##### Lab Orders Tracking
- View all lab orders with patient information
- Order status and creation tracking
- Patient name resolution

##### Lab Results Viewing
- Safe JSON decoding for result data
- Handle double-encoded JSON structures
- Detailed result presentation

##### Diet Orders Tracking
- Nutritional order management
- Patient linkage and status tracking

##### Surgery Orders Tracking
- Surgical procedure scheduling
- Proposed date and time management
- Patient and procedure details

#### Database Tables
- `lab_orders_core1`: Laboratory test orders
- `diet_orders_core1`: Nutritional orders
- `surgery_orders_core1`: Surgical procedure orders
- `patients_core1`: Patient information

## Audit System

### Controller: AdminAuditController

Provides comprehensive audit logging and reporting.

#### Features

##### Audit Dashboard
- Complete audit log viewing
- Fleet integration for vehicle references
- Cost and activity tracking

##### Audit Analytics
- Total expenses calculation
- Maintenance activity counting
- Category-based filtering

##### Audit Details
- Action tracking with performer information
- Cost recording for financial analysis
- Reference linking to operational records

#### Database Tables
- `audit_logistics2`: Comprehensive audit logs
- `fleet_management_logistics2`: Vehicle reference data

## Warehouse Purchase Orders

### Controller: AdminWarehousePurchaseOrdersController

Manages warehouse-level purchase order processing.

#### Features

##### Order Overview
- View all warehouse purchase orders
- Status tracking and management

##### Vehicle Assignment
- Assign available vehicles to orders
- Model name specification
- Status updates to approved

##### Fleet Availability
- Real-time available vehicle listing
- Vehicle selection for assignments

#### Database Tables
- `warehouse_purchaseorders_logistics1`: Warehouse purchase orders
- `fleet_management_logistics2`: Available vehicles

## Workflow Integration

### Module Dependencies
- **Logistics1**: Provides procurement requests and warehouse orders
- **Core1**: Supplies medical document data for tracking
- **Core2**: Receives inventory updates from deliveries

### Data Flow
1. **Procurement**: Logistics1 requests → Logistics2 processing → Vendor coordination → Vehicle assignment → Shipment → Delivery → Core2 inventory update
2. **Fleet**: Vehicle management → Reservation → Transit → Delivery → Maintenance → Audit logging
3. **Documents**: Core1 orders → Logistics2 tracking → Status monitoring
4. **Audit**: All operations → Audit logging → Financial reporting

## Business Rules

### Fleet Rules
- Unique plate number requirement
- Status transitions: available → in_use → maintenance → available
- Vehicle type categorization

### Vendor Rules
- Automatic vendor code generation
- Accreditation expiry tracking
- Soft delete for data preservation

### Reservation Rules
- Cost recording required for transit
- Status cascade updates across tables
- Employee attribution for accountability

### Document Rules
- Patient information linkage required
- Safe JSON handling for result data
- Chronological ordering by creation/proposed dates

### Audit Rules
- All significant actions logged
- Cost tracking for financial analysis
- Performer identification required

## Security Considerations

- **Role-Based Access**: Strict enforcement of admin_logistics2 role
- **Data Validation**: Comprehensive input validation
- **Transaction Safety**: Database transactions for multi-table operations
- **Audit Trail**: Complete logging of all operational activities

## Performance Optimizations

- **Efficient Queries**: Optimized joins for data retrieval
- **Pagination**: Large datasets handled appropriately
- **Status Filtering**: Database-level filtering for performance
- **Transaction Batching**: Multi-operation transactions for data integrity

## Error Handling

- Graceful error messages for validation failures
- Transaction rollbacks for failed operations
- Existence checks before updates
- User-friendly feedback via session messages

## Integration Points

### External Systems
- **Logistics1**: Procurement request source
- **Core1**: Medical document data
- **Core2**: Inventory management
- **Financial Systems**: Cost and audit data

### API Considerations
- RESTful endpoints for status updates
- Secure authentication for operations
- JSON responses for AJAX integrations

## Future Enhancements

### Planned Features
- Automated vehicle routing optimization
- Real-time GPS tracking integration
- Predictive maintenance scheduling
- Advanced vendor performance analytics
- Mobile app for driver communications

### Scalability Considerations
- Database partitioning for large audit logs
- Caching for frequently accessed fleet data
- Background job processing for bulk operations
- API rate limiting for external integrations

## Technical Implementation

### Controller Architecture
- Consistent authorization checks
- Database transaction usage for complex operations
- Employee resolution for user attribution
- Validation rules for data consistency

### Data Relationships
- Vehicle reservations link to vendor logs and purchase orders
- Audit logs reference operational records
- Document tracking connects to patient data
- Fleet management integrates with reservations

### Status Management
- Procurement: pending → approved → processing → shipped → delivered
- Vehicles: available → in_use → maintenance → available
- Vendors: active → inactive (soft delete)
- Documents: ordered → in_progress → completed

## Monitoring and Reporting

### Key Metrics
- Fleet utilization rates
- Delivery completion times
- Vendor performance scores
- Audit activity volumes
- Cost per delivery

### Reporting Capabilities
- Procurement fulfillment reports
- Fleet maintenance logs
- Vendor performance analytics
- Document processing statistics
- Financial cost analysis

## Compliance Considerations

### Logistics Standards
- Vehicle maintenance regulations
- Supplier accreditation requirements
- Document retention policies
- Financial audit compliance

### Data Security
- Vendor sensitive information protection
- Patient data privacy in document tracking
- Financial transaction security
- Access control enforcement
