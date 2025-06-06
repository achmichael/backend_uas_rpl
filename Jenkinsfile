pipeline {
    agent any

    environment {
        IMAGE_NAME = 'backend-rpl-frankenphp:latest'
        CONTAINER_NAME = 'backend_frankenphp'
        DOCKER_BUILDKIT = 1
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Remove existing container')
        {
            steps {
                sh 'docker compose down'
            }
        }

        stage('Clean up db container') {
            steps {
                sh '''
                docker rm -f database_server || true
                '''
            }
        }

        stage('Remove existing image'){
            steps {
                sh 'docker rmi -f ${IMAGE_NAME}'
            }
        }
        
        stage('Preparing .env file') {
            steps {
                sh 'cp .env.example .env'
            }
        }

        stage('Build Docker Image') {
            steps {
                sh 'docker compose up -d'
            }
        }
    }
}
