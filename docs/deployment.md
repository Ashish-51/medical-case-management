# AWS Deployment Architecture & Strategy

This document outlines how the **Medical Case Management Application** would be architected and deployed on Amazon Web Services (AWS) in a high-availability, HIPAA/GDPR-aligned cloud topology.

---

## Architecture Overview

```
                      Internet
                         │
                         ▼
             [ AWS Route 53 (DNS) ]
                         │
                         ▼
        [ AWS Certificate Manager (ACM) - SSL/TLS ]
                         │
                         ▼
     [ Application Load Balancer (ALB) - HTTPS :443 ]
                         │
        ┌────────────────┴────────────────┐
        ▼                                 ▼
[ EC2 (AZ-1) - App ]              [ EC2 (AZ-2) - App ]
 (Nginx + PHP 8.2 FPM)             (Nginx + PHP 8.2 FPM)
        │                                 │
        ├────────────────┬────────────────┤
        │                │                │
        ▼                ▼                ▼
[ AWS RDS Multi-AZ ] [ Amazon S3 ]   [ AWS Secrets Manager / ]
  (MySQL 8.0 Engine)   (Implant CAD    [ Systems Manager SSM   ]
                       & DICOM Assets) (Env Variables & Keys)
```

---

## Component Details

### 1. Compute: EC2 with Auto Scaling & ALB
- **Web / Application Server**: AWS EC2 instances running Ubuntu LTS with Nginx and PHP 8.2-FPM.
- **High Availability**: Instances deployed across multiple Availability Zones (Multi-AZ) behind an **Application Load Balancer (ALB)**.
- **Auto Scaling Group (ASG)**: Scales instance capacity horizontally based on CPU utilization and incoming HTTP request volume.

### 2. Database: AWS RDS for MySQL
- **Engine**: MySQL 8.0 with Multi-AZ automated standby replica for automatic failover without data loss.
- **Storage**: General Purpose SSD (gp3) or Provisioned IOPS with automated daily backups and point-in-time recovery enabled.
- **Security**: Placed strictly in private VPC subnets with no public internet ingress.

### 3. Storage: Amazon S3 (Files & Attachments)
- Secure, encrypted object storage for patient reports, DICOM imaging scans, and custom CAD implant model files.
- Server-side encryption using **AWS KMS (SSE-KMS)**.
- Access controlled through Pre-Signed URLs with strict short-lived TTLs.

### 4. Security Groups & Network Isolation (VPC)
- **Public Subnet**: Holds only the Application Load Balancer (ALB). Accepts HTTPS port 443 traffic from the internet.
- **Private App Subnet**: Holds EC2 instances. Security group accepts HTTP port 80/8080 ingress **strictly from the ALB security group**.
- **Private Data Subnet**: Holds MySQL RDS and ElastiCache Redis. Accepts port 3306 ingress **strictly from the EC2 application security group**.
- **No Direct SSH**: Remote administration performed using **AWS Systems Manager (SSM) Session Manager** without opening inbound port 22.

### 5. Environment Secrets & Configuration
- Production environment parameters (DB passwords, app secrets, Sanctum signing tokens) stored securely in **AWS Secrets Manager** or **AWS Systems Manager Parameter Store (SecureString)**.
- EC2 IAM instance profiles grant least-privilege IAM roles to fetch secrets at boot without hardcoding `.env` credentials in source control or disk images.

### 6. SSL / TLS & HTTPS Enforcement
- **AWS Certificate Manager (ACM)** generates and automatically renews SSL certificates.
- The ALB terminates HTTPS on port 443 and issues automated `301 Moved Permanently` redirects for all incoming port 80 HTTP requests.
