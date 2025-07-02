pipeline {
    agent any
    
    environment {
        DEPLOY_SERVER = 'jd@212.85.25.94'
        DEPLOY_PATH   = '/var/www/html/sunset/ums'
        DEPLOY_PATH_TEST = '/var/www/html/sunset/ums-test'
        SSH_CRED      = '7f5db0fc-1f49-44d1-827b-9f8fbee846ea'
    }

    stages {
        stage('Deploy TEST Production') {
            steps {
                sshagent (credentials: [env.SSH_CRED]) {
                    withCredentials([
                        file(credentialsId: 'ums_prod_test', variable: 'ENV_PROD_TEST'),
                        file(credentialsId: 'ums_prod_testing', variable: 'ENV_PROD_TESTING')
                    ]) {
                        // Set permissions
                        sh """
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                sudo chown -R jd:www-data /var/www/html/sunset/ums-test &&
                                sudo chmod -R 755 /var/www/html/sunset/ums-test
                            '
                        """
                        
                        // Git pull with prune
                        sh """
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                cd /var/www/html/sunset/ums-test &&
                                if [ ! -d ".git" ]; then
                                    git clone https://github.com/Jdv2022/project_1_user_management_service . 
                                else
                                    git fetch --prune
                                    git reset --hard origin/main
                                    git clean -fd
                                fi
                            '
                        """
                
                        // Upload .env files
                        sh """
                            scp -o StrictHostKeyChecking=no \$ENV_PROD_TEST jd@212.85.25.94:/var/www/html/sunset/ums/.env
                            scp -o StrictHostKeyChecking=no \$ENV_PROD_TESTING jd@212.85.25.94:/var/www/html/sunset/ums/.env.testing
                        
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                sudo chmod 644 /var/www/html/sunset/ums/.env &&
                                sudo chmod 644 /var/www/html/sunset/ums/.env.testing
                            '
                        """
                        
                        sh """
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                cd /var/www/html/sunset/ums &&
                                composer install
                            '
                        """
                    
                        sh """
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                sudo chown -R jd:www-data /var/www/html/sunset/ums &&
                                sudo chmod -R 755 /var/www/html/sunset/ums
                            '
                        """
                        
                        sh """
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                cd /var/www/html/sunset/ums &&
                                docker compose up -d
                            '
                        """
						sh """
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                cd /var/www/html/sunset/ums &&
                                docker compose exec app ./vendor/bin/phpunit
                            '
                        """
						sh """
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                cd /var/www/html/sunset/ums &&
                                docker compose down &&
        						docker network prune -f
                            '
                        """
                    }
                }
            }
        }
		stage('Deploy ACTUAL Production') {
            steps {
                sshagent (credentials: [env.SSH_CRED]) {
                    withCredentials([
                        file(credentialsId: 'ums_prod_env', variable: 'ENV_FILE'),
                    ]) {
                        // Set permissions
                        sh """
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                sudo chown -R jd:www-data /var/www/html/sunset/ums &&
                                sudo chmod -R 755 /var/www/html/sunset/ums
                            '
                        """
                        
                        // Git pull with prune
                        sh """
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                cd /var/www/html/sunset/ums &&
                                if [ ! -d ".git" ]; then
                                    git clone https://github.com/Jdv2022/project_1_user_management_service . 
                                else
                                    git fetch --prune
                                    git reset --hard origin/main
                                    git clean -fd
                                fi
                            '
                        """
                
                        // Upload .env files
                        sh """
                            scp -o StrictHostKeyChecking=no \$ENV_FILE jd@212.85.25.94:/var/www/html/sunset/ums/.env
                        
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                sudo chmod 644 /var/www/html/sunset/ums/.env
                            '
                        """
                        
                        sh """
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                cd /var/www/html/sunset/ums &&
                                composer install
                            '
                        """
                    
                        sh """
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                sudo chown -R jd:www-data /var/www/html/sunset/ums &&
                                sudo chmod -R 755 /var/www/html/sunset/ums
                            '
                        """
                        
                        sh """
                            ssh -o StrictHostKeyChecking=no jd@212.85.25.94 '
                                cd /var/www/html/sunset/ums &&
                                docker compose up -d
                            '
                        """
                    }
                }
            }
        }
    }
}