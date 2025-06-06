pipeline {
    agent any

    environment {
        IMAGE_NAME = 'backend_uas_rpl-frankenphp'
        CONTAINER_NAME = 'backend_frankenphp'
        DOCKER_BUILDKIT = 1
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                sh 'docker compose up -d'
            }
        }
    }
}
