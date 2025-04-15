pipeline {
    agent any

    environment {
        APP_ENV = 'prod'
        APP_DEBUG = 'false'
        DATABASE_URL = 'mysql://api-user:JGVbc\02YGBY4hsPKJTN@db:3306/api_guild_swgoh'
        CORS_ALLOW_ORIGIN = *
        MYSQL_DATABASE = 'api_guild_swgoh'
        MYSQL_USER = 'api-user'
        MYSQL_PASSWORD = 'JGVbc\02YGBY4hsPKJTN'
        MYSQL_ROOT_PASSWORD = 'JGVbc\02YGBY4hsPKJTN'
    }

    stages {
        stage('Copy Secret File') {
            steps {
                // Utiliser withCredentials pour récupérer le fichier secret et le copier
                withCredentials([file(credentialsId: 'secret_api_swgoh', variable: 'SECRET_ENV_FILE')]) {
                    // Le fichier secret est copié et disponible sous la variable $SECRET_ENV_FILE
                    sh 'cp -f $SECRET_ENV_FILE ./api/.env'
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                script {
                    sh 'cd ./api && composer install --no-interaction'
                }
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    sh 'docker-compose -f docker-compose.yaml up --build -d'
                }
            }
        }

        stage('Deploy') {
            steps {
                script {
                    sh 'docker-compose -f docker-compose.yaml down && docker-compose -f docker-compose.yaml up -d'
                }
            }
        }
    }
}