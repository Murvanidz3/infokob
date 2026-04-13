<?php
/**
 * Property Model
 * Handles all database operations for properties
 */

class Property {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all properties with filters, pagination, and translations
     */
    public function getAll($filters = [], $page = 1, $perPage = ITEMS_PER_PAGE) {
        // Auto-expire featured listings
        $this->expireFeatured();
        
        $lang = Language::get();
        $where = ["p.status = 'active'"];
        $params = [];
        
        // Filter by type
        if (!empty($filters['type'])) {
            if (is_array($filters['type'])) {
                $placeholders = [];
                foreach ($filters['type'] as $i => $t) {
                    $key = ':type_' . $i;
                    $placeholders[] = $key;
                    $params[$key] = $t;
                }
                $where[] = 'p.type IN (' . implode(',', $placeholders) . ')';
            } else {
                $where[] = 'p.type = :type';
                $params[':type'] = $filters['type'];
            }
        }
        
        // Filter by deal type
        if (!empty($filters['deal_type'])) {
            $where[] = 'p.deal_type = :deal_type';
            $params[':deal_type'] = $filters['deal_type'];
        }
        
        // Filter by price range
        if (!empty($filters['price_min'])) {
            $where[] = 'p.price >= :price_min';
            $params[':price_min'] = $filters['price_min'];
        }
        if (!empty($filters['price_max'])) {
            $where[] = 'p.price <= :price_max';
            $params[':price_max'] = $filters['price_max'];
        }
        
        // Filter by rooms
        if (!empty($filters['rooms'])) {
            if ($filters['rooms'] >= 5) {
                $where[] = 'p.rooms >= 5';
            } else {
                $where[] = 'p.rooms = :rooms';
                $params[':rooms'] = $filters['rooms'];
            }
        }
        
        // Filter by district
        if (!empty($filters['district'])) {
            $where[] = 'p.district = :district';
            $params[':district'] = $filters['district'];
        }
        
        // Filter by sea distance
        if (!empty($filters['sea_distance'])) {
            $where[] = 'p.sea_distance_m <= :sea_distance';
            $params[':sea_distance'] = $filters['sea_distance'];
        }
        
        $whereSQL = implode(' AND ', $where);
        
        // Count total
        $countSQL = "SELECT COUNT(*) FROM properties p WHERE {$whereSQL}";
        $countStmt = $this->db->prepare($countSQL);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();
        
        $pagination = paginate($total, $page, $perPage);
        
        // Sort order
        $sort = $filters['sort'] ?? 'newest';
        $orderBy = match($sort) {
            'cheapest' => 'p.price ASC',
            'expensive' => 'p.price DESC',
            'popular' => 'p.views DESC',
            default => 'p.is_featured DESC, p.created_at DESC',
        };
        
        // Main query with translations and main image
        $sql = "
            SELECT p.*, 
                   pt.title, pt.description,
                   (SELECT pi.filename FROM property_images pi 
                    WHERE pi.property_id = p.id AND pi.is_main = 1 LIMIT 1) as main_image,
                   u.name as user_name, u.phone as user_phone,
                   u.whatsapp_number as user_whatsapp,
                   u.telegram_username as user_telegram
            FROM properties p
            LEFT JOIN property_translations pt ON pt.property_id = p.id AND pt.lang = :lang
            LEFT JOIN users u ON u.id = p.user_id
            WHERE {$whereSQL}
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ";
        
        $params[':lang'] = $lang;
        $params[':limit'] = $perPage;
        $params[':offset'] = $pagination['offset'];
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $type);
        }
        $stmt->execute();
        $properties = $stmt->fetchAll();
        
        return [
            'data' => $properties,
            'pagination' => $pagination,
        ];
    }
    
    /**
     * Get featured listings
     */
    public function getFeatured($limit = 6) {
        $lang = Language::get();
        $this->expireFeatured();
        
        $sql = "
            SELECT p.*, 
                   pt.title, pt.description,
                   (SELECT pi.filename FROM property_images pi 
                    WHERE pi.property_id = p.id AND pi.is_main = 1 LIMIT 1) as main_image
            FROM properties p
            LEFT JOIN property_translations pt ON pt.property_id = p.id AND pt.lang = :lang
            WHERE p.status = 'active' AND p.is_featured = 1
            ORDER BY p.created_at DESC
            LIMIT :limit
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lang', $lang);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $featured = $stmt->fetchAll();
        
        // If not enough featured, fill with newest active
        if (count($featured) < $limit) {
            $remaining = $limit - count($featured);
            $existingIds = array_column($featured, 'id');
            $excludeSQL = '';
            $excludeParams = [':lang2' => $lang, ':remaining' => $remaining];
            
            if (!empty($existingIds)) {
                $placeholders = [];
                foreach ($existingIds as $i => $id) {
                    $k = ':excl_' . $i;
                    $placeholders[] = $k;
                    $excludeParams[$k] = $id;
                }
                $excludeSQL = 'AND p.id NOT IN (' . implode(',', $placeholders) . ')';
            }

            $sql2 = "
                SELECT p.*, 
                       pt.title, pt.description,
                       (SELECT pi.filename FROM property_images pi 
                        WHERE pi.property_id = p.id AND pi.is_main = 1 LIMIT 1) as main_image
                FROM properties p
                LEFT JOIN property_translations pt ON pt.property_id = p.id AND pt.lang = :lang2
                WHERE p.status = 'active' {$excludeSQL}
                ORDER BY p.created_at DESC
                LIMIT :remaining
            ";
            
            $stmt2 = $this->db->prepare($sql2);
            foreach ($excludeParams as $k => $v) {
                $stmt2->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt2->execute();
            $featured = array_merge($featured, $stmt2->fetchAll());
        }
        
        return $featured;
    }
    
    /**
     * Get single property by slug
     */
    public function getBySlug($slug) {
        $lang = Language::get();
        
        $sql = "
            SELECT p.*, 
                   pt.title, pt.description,
                   u.name as user_name, u.phone as user_phone, u.email as user_email,
                   u.whatsapp_number as user_whatsapp,
                   u.telegram_username as user_telegram
            FROM properties p
            LEFT JOIN property_translations pt ON pt.property_id = p.id AND pt.lang = :lang
            LEFT JOIN users u ON u.id = p.user_id
            WHERE p.slug = :slug AND p.status IN ('active', 'sold')
            LIMIT 1
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':slug' => $slug, ':lang' => $lang]);
        $property = $stmt->fetch();
        
        if ($property) {
            // Get all images
            $imgStmt = $this->db->prepare("
                SELECT * FROM property_images 
                WHERE property_id = :pid 
                ORDER BY is_main DESC, sort_order ASC
            ");
            $imgStmt->execute([':pid' => $property['id']]);
            $property['images'] = $imgStmt->fetchAll();
            
            // Get main image
            $property['main_image'] = '';
            foreach ($property['images'] as $img) {
                if ($img['is_main']) {
                    $property['main_image'] = $img['filename'];
                    break;
                }
            }
            if (empty($property['main_image']) && !empty($property['images'])) {
                $property['main_image'] = $property['images'][0]['filename'];
            }
        }
        
        return $property;
    }
    
    /**
     * Get properties by user ID
     */
    public function getByUser($userId) {
        $lang = Language::get();
        
        $sql = "
            SELECT p.*, 
                   pt.title,
                   (SELECT pi.filename FROM property_images pi 
                    WHERE pi.property_id = p.id AND pi.is_main = 1 LIMIT 1) as main_image
            FROM properties p
            LEFT JOIN property_translations pt ON pt.property_id = p.id AND pt.lang = :lang
            WHERE p.user_id = :user_id
            ORDER BY p.created_at DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':lang' => $lang]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get single property by ID (for editing)
     */
    public function getById($id) {
        $sql = "SELECT * FROM properties WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $property = $stmt->fetch();
        
        if ($property) {
            // Get all translations
            $transStmt = $this->db->prepare("SELECT * FROM property_translations WHERE property_id = :pid");
            $transStmt->execute([':pid' => $id]);
            $property['translations'] = [];
            foreach ($transStmt->fetchAll() as $t) {
                $property['translations'][$t['lang']] = $t;
            }
            
            // Get images
            $imgStmt = $this->db->prepare("
                SELECT * FROM property_images WHERE property_id = :pid ORDER BY is_main DESC, sort_order ASC
            ");
            $imgStmt->execute([':pid' => $id]);
            $property['images'] = $imgStmt->fetchAll();
        }
        
        return $property;
    }
    
    /**
     * Create a new property
     */
    public function create($data) {
        $this->db->beginTransaction();
        
        try {
            // Insert property
            $sql = "
                INSERT INTO properties 
                (user_id, slug, type, deal_type, status, price, currency, price_negotiable,
                 area_m2, rooms, bedrooms, bathrooms, floors_total, floor_number,
                 has_pool, has_garage, has_balcony, has_garden, has_furniture,
                 sea_distance_m, address, district, lat, lng,
                 contact_name, contact_phone, contact_whatsapp, contact_telegram, contact_email)
                VALUES 
                (:user_id, :slug, :type, :deal_type, 'pending', :price, :currency, :negotiable,
                 :area_m2, :rooms, :bedrooms, :bathrooms, :floors_total, :floor_number,
                 :has_pool, :has_garage, :has_balcony, :has_garden, :has_furniture,
                 :sea_distance_m, :address, :district, :lat, :lng,
                 :contact_name, :contact_phone, :contact_whatsapp, :contact_telegram, :contact_email)
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id'      => $data['user_id'],
                ':slug'         => $data['slug'],
                ':type'         => $data['type'],
                ':deal_type'    => $data['deal_type'],
                ':price'        => $data['price'],
                ':currency'     => $data['currency'],
                ':negotiable'   => $data['price_negotiable'] ?? 0,
                ':area_m2'      => $data['area_m2'] ?? null,
                ':rooms'        => $data['rooms'] ?? null,
                ':bedrooms'     => $data['bedrooms'] ?? null,
                ':bathrooms'    => $data['bathrooms'] ?? null,
                ':floors_total' => $data['floors_total'] ?? null,
                ':floor_number' => $data['floor_number'] ?? null,
                ':has_pool'     => $data['has_pool'] ?? 0,
                ':has_garage'   => $data['has_garage'] ?? 0,
                ':has_balcony'  => $data['has_balcony'] ?? 0,
                ':has_garden'   => $data['has_garden'] ?? 0,
                ':has_furniture' => $data['has_furniture'] ?? 0,
                ':sea_distance_m' => $data['sea_distance_m'] ?? null,
                ':address'      => $data['address'] ?? '',
                ':district'     => $data['district'] ?? '',
                ':lat'          => $data['lat'] ?? null,
                ':lng'          => $data['lng'] ?? null,
                ':contact_name' => $data['contact_name'] ?? '',
                ':contact_phone' => $data['contact_phone'] ?? '',
                ':contact_whatsapp' => $data['contact_whatsapp'] ?? '',
                ':contact_telegram' => $data['contact_telegram'] ?? '',
                ':contact_email' => $data['contact_email'] ?? '',
            ]);
            
            $propertyId = $this->db->lastInsertId();
            
            // Insert translations
            if (!empty($data['translations'])) {
                $transSql = "INSERT INTO property_translations (property_id, lang, title, description) VALUES (:pid, :lang, :title, :desc)";
                $transStmt = $this->db->prepare($transSql);
                
                foreach ($data['translations'] as $lang => $trans) {
                    if (!empty($trans['title'])) {
                        $transStmt->execute([
                            ':pid'   => $propertyId,
                            ':lang'  => $lang,
                            ':title' => $trans['title'],
                            ':desc'  => $trans['description'] ?? '',
                        ]);
                    }
                }
            }
            
            // Insert images
            if (!empty($data['images'])) {
                $imgSql = "INSERT INTO property_images (property_id, filename, is_main, sort_order) VALUES (:pid, :filename, :is_main, :sort)";
                $imgStmt = $this->db->prepare($imgSql);
                
                foreach ($data['images'] as $i => $filename) {
                    $imgStmt->execute([
                        ':pid'      => $propertyId,
                        ':filename' => $filename,
                        ':is_main'  => $i === 0 ? 1 : 0,
                        ':sort'     => $i,
                    ]);
                }
            }
            
            $this->db->commit();
            cache_clear('homepage');
            return $propertyId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Update a property
     */
    public function update($id, $data) {
        $this->db->beginTransaction();
        
        try {
            $fields = [];
            $params = [':id' => $id];
            
            $allowedFields = [
                'type', 'deal_type', 'price', 'currency', 'price_negotiable',
                'area_m2', 'rooms', 'bedrooms', 'bathrooms', 'floors_total', 'floor_number',
                'has_pool', 'has_garage', 'has_balcony', 'has_garden', 'has_furniture',
                'sea_distance_m', 'address', 'district', 'lat', 'lng',
                'contact_name', 'contact_phone', 'contact_whatsapp', 'contact_telegram',
                'contact_email', 'status', 'is_featured', 'featured_until', 'admin_note'
            ];
            
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $fields[] = "`{$field}` = :{$field}";
                    $params[":{$field}"] = $data[$field];
                }
            }
            
            if (!empty($fields)) {
                $sql = "UPDATE properties SET " . implode(', ', $fields) . " WHERE id = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            }
            
            // Update translations
            if (!empty($data['translations'])) {
                foreach ($data['translations'] as $lang => $trans) {
                    if (!empty($trans['title'])) {
                        $sql = "INSERT INTO property_translations (property_id, lang, title, description) 
                                VALUES (:pid, :lang, :title, :desc) 
                                ON DUPLICATE KEY UPDATE title = :title2, description = :desc2";
                        $stmt = $this->db->prepare($sql);
                        $stmt->execute([
                            ':pid'   => $id,
                            ':lang'  => $lang,
                            ':title' => $trans['title'],
                            ':desc'  => $trans['description'] ?? '',
                            ':title2' => $trans['title'],
                            ':desc2'  => $trans['description'] ?? '',
                        ]);
                    }
                }
            }
            
            // Handle new images
            if (!empty($data['new_images'])) {
                $maxSort = $this->db->prepare("SELECT COALESCE(MAX(sort_order), -1) FROM property_images WHERE property_id = :pid");
                $maxSort->execute([':pid' => $id]);
                $sortStart = (int) $maxSort->fetchColumn() + 1;
                
                $imgStmt = $this->db->prepare("INSERT INTO property_images (property_id, filename, is_main, sort_order) VALUES (:pid, :filename, 0, :sort)");
                foreach ($data['new_images'] as $i => $filename) {
                    $imgStmt->execute([
                        ':pid' => $id,
                        ':filename' => $filename,
                        ':sort' => $sortStart + $i,
                    ]);
                }
            }
            
            $this->db->commit();
            cache_clear('homepage');
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Delete a property and its images
     */
    public function delete($id) {
        // Get images first
        $imgStmt = $this->db->prepare("SELECT filename FROM property_images WHERE property_id = :pid");
        $imgStmt->execute([':pid' => $id]);
        $images = $imgStmt->fetchAll();
        
        // Delete from DB (cascades to translations and images)
        $stmt = $this->db->prepare("DELETE FROM properties WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        // Delete image files
        foreach ($images as $img) {
            Image::delete($img['filename']);
        }
        
        cache_clear('homepage');
        return true;
    }
    
    /**
     * Delete a specific image
     */
    public function deleteImage($imageId) {
        $stmt = $this->db->prepare("SELECT * FROM property_images WHERE id = :id");
        $stmt->execute([':id' => $imageId]);
        $image = $stmt->fetch();
        
        if ($image) {
            $this->db->prepare("DELETE FROM property_images WHERE id = :id")->execute([':id' => $imageId]);
            Image::delete($image['filename']);
            
            // If deleted was main, set first remaining as main
            if ($image['is_main']) {
                $this->db->prepare("
                    UPDATE property_images SET is_main = 1 
                    WHERE property_id = :pid 
                    ORDER BY sort_order ASC LIMIT 1
                ")->execute([':pid' => $image['property_id']]);
            }
        }
    }
    
    /**
     * Increment view counter
     */
    public function incrementViews($id) {
        $stmt = $this->db->prepare("UPDATE properties SET views = views + 1 WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
    
    /**
     * Get homepage stats
     */
    public function getStats() {
        $cached = cache_get('homepage_stats');
        if ($cached) return $cached;
        
        $stats = [];
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM properties WHERE status = 'active'");
        $stats['listings'] = (int)$stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
        $stats['users'] = (int)$stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM properties WHERE status = 'sold'");
        $stats['sold'] = (int)$stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT MIN(sea_distance_m) FROM properties WHERE status = 'active' AND sea_distance_m > 0");
        $stats['min_sea'] = (int)$stmt->fetchColumn();
        
        cache_set('homepage_stats', $stats, 1800);
        return $stats;
    }
    
    /**
     * Get user stats for dashboard
     */
    public function getUserStats($userId) {
        $stats = [];
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM properties WHERE user_id = :uid AND status = 'active'");
        $stmt->execute([':uid' => $userId]);
        $stats['active'] = (int)$stmt->fetchColumn();
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM properties WHERE user_id = :uid AND status = 'pending'");
        $stmt->execute([':uid' => $userId]);
        $stats['pending'] = (int)$stmt->fetchColumn();
        
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(views), 0) FROM properties WHERE user_id = :uid");
        $stmt->execute([':uid' => $userId]);
        $stats['views'] = (int)$stmt->fetchColumn();
        
        return $stats;
    }
    
    /**
     * Get similar properties
     */
    public function getSimilar($property, $limit = 3) {
        $lang = Language::get();
        
        $sql = "
            SELECT p.*, 
                   pt.title,
                   (SELECT pi.filename FROM property_images pi 
                    WHERE pi.property_id = p.id AND pi.is_main = 1 LIMIT 1) as main_image
            FROM properties p
            LEFT JOIN property_translations pt ON pt.property_id = p.id AND pt.lang = :lang
            WHERE p.status = 'active' 
              AND p.id != :id
              AND (p.type = :type OR p.district = :district)
            ORDER BY 
              CASE WHEN p.type = :type2 AND p.district = :district2 THEN 0
                   WHEN p.type = :type3 THEN 1
                   ELSE 2 END,
              p.created_at DESC
            LIMIT :limit
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lang', $lang);
        $stmt->bindValue(':id', $property['id'], PDO::PARAM_INT);
        $stmt->bindValue(':type', $property['type']);
        $stmt->bindValue(':type2', $property['type']);
        $stmt->bindValue(':type3', $property['type']);
        $stmt->bindValue(':district', $property['district'] ?? '');
        $stmt->bindValue(':district2', $property['district'] ?? '');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Auto-expire featured listings
     */
    private function expireFeatured() {
        $this->db->exec("
            UPDATE properties 
            SET is_featured = 0 
            WHERE is_featured = 1 
              AND featured_until IS NOT NULL 
              AND featured_until < NOW()
        ");
    }
    
    /**
     * Check if slug exists (for unique slug generation)
     */
    public function slugExists($slug, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM properties WHERE slug = :slug";
        $params = [':slug' => $slug];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
    
    /**
     * Generate unique slug
     */
    public function generateSlug($title, $excludeId = null) {
        $baseSlug = slug($title);
        $slug = $baseSlug;
        $counter = 1;
        
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    // ─── Admin Methods ─────────────────────────────────────
    
    /**
     * Get all properties for admin (any status)
     */
    public function adminGetAll($filters = [], $page = 1) {
        $lang = Language::get();
        $where = ['1=1'];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = 'p.status = :status';
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = '(pt.title LIKE :search OR p.slug LIKE :search2)';
            $params[':search'] = '%' . $filters['search'] . '%';
            $params[':search2'] = '%' . $filters['search'] . '%';
        }
        
        $whereSQL = implode(' AND ', $where);
        
        $countSQL = "
            SELECT COUNT(*) FROM properties p
            LEFT JOIN property_translations pt ON pt.property_id = p.id AND pt.lang = 'ka'
            WHERE {$whereSQL}
        ";
        $countStmt = $this->db->prepare($countSQL);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();
        
        $pagination = paginate($total, $page, 20);
        
        $sql = "
            SELECT p.*, 
                   pt.title,
                   (SELECT pi.filename FROM property_images pi 
                    WHERE pi.property_id = p.id AND pi.is_main = 1 LIMIT 1) as main_image,
                   u.name as user_name, u.email as user_email
            FROM properties p
            LEFT JOIN property_translations pt ON pt.property_id = p.id AND pt.lang = 'ka'
            LEFT JOIN users u ON u.id = p.user_id
            WHERE {$whereSQL}
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $params[':limit'] = 20;
        $params[':offset'] = $pagination['offset'];
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        
        return [
            'data' => $stmt->fetchAll(),
            'pagination' => $pagination,
        ];
    }
    
    /**
     * Get pending count for admin badge
     */
    public function getPendingCount() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM properties WHERE status = 'pending'");
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Get admin dashboard stats
     */
    public function adminGetStats() {
        $stats = [];
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM properties");
        $stats['total'] = (int)$stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM properties WHERE status = 'pending'");
        $stats['pending'] = (int)$stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM users");
        $stats['users'] = (int)$stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COALESCE(SUM(views), 0) FROM properties");
        $stats['views'] = (int)$stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM properties WHERE is_featured = 1");
        $stats['featured'] = (int)$stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM properties WHERE is_featured = 1 AND featured_until IS NOT NULL AND featured_until < NOW()");
        $stats['featured_expired'] = (int)$stmt->fetchColumn();
        
        return $stats;
    }
}
